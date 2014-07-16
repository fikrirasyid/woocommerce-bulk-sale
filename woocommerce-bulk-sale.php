<?php
/*
    Plugin Name: WooCommerce Bulk Sale
    Version: 0.1
    Description: Enabling user to set sale status for many products at the same time
    Author: Fikri Rasyid
    Author URI: http://fikrirasyid.com
*/
/*
    Copyright 2014 Fikri Rasyid
    Developed by Fikri Rasyid (fikrirasyid@gmail.com)
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    /**
     * If the plugin is called before woocommerce, we need to include it first
     */
    if( !class_exists( 'Woocommerce' ) )
        include_once( ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php' );
	
	class Woocommerce_Bulk_Sale{

		var $plugin_url;
		var $plugin_dir;
		var $current_time;

		/**
		 * Init the method
		 */
		function __construct(){
			$this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
			$this->plugin_dir = plugin_dir_path( __FILE__ );
			$this->current_time = current_time( 'timestamp' );

			// Register activation task
			register_activation_hook( __FILE__, array( $this, 'activation' ) );

			// Register deactivation task
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

			// Enqueueing scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// Add submenu
			add_action( 'admin_menu', array( $this, 'add_page' ) );

			// Refresh scheduled sales every 5 minutes
			add_action( 'woocommerce_scheduled_sales_micro', 'wc_scheduled_sales' );
		}

		/**
		 * Register new interval
		 * 
		 * @return array of modified schedule
		 */
		function cron_five_minutes( $schedule ){
			$schedules['every5minutes'] = array(
				'interval' => 300,
				'display' => __( 'Every 5 minutes', 'woocommerce-sale-timepicker' )
			);

			return $schedules;
		}

		/**
		 * Activation task
		 * 
		 * @return void
		 */
		function activation(){
			if( !wp_next_scheduled( 'woocommerce_scheduled_sales_micro' ) ){
				wp_schedule_event( current_time( 'timestamp', wp_timezone_override_offset() ), 'every5minutes', 'woocommerce_scheduled_sales_micro' );
			}

		}

		/**
		 * Deactivation task
		 * 
		 * @return void
		 */
		function deactivation(){
			wp_clear_scheduled_hook( 'woocommerce_scheduled_sales_micro' );
		}

		/**
		 * Register and enqueue script
		 */
		function admin_scripts(){
			wp_register_script( 'jquery-ui-timepicker', $this->plugin_url . '/js/jquery-ui-timepicker.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ) );
			wp_register_script( 'woocommerce_bulk_sale', $this->plugin_url . '/js/woocommerce-bulk-sale.js', array( 'jquery-ui-timepicker' ) );

			// Get current screen estate
			$screen = get_current_screen();

			// Only enqueue the script on bulk sale screen
			if( 'product_page_woocommerce-bulk-sale' == $screen->id ){
		    	wp_enqueue_script( 'woocommerce_bulk_sale' );
			}
		}

		/**
		 * Register the page
		 * 
		 * @return void
		 */
		function add_page(){
			add_submenu_page( 
				'edit.php?post_type=product', 
				__( 'Bulk Sale', 'woocommerce-bulk-sale' ), 
				__( 'Bulk Sale', 'woocommerce-bulk-sale' ), 
				'manage_woocommerce', 
				'woocommerce-bulk-sale', 
				array( $this, 'render_page' ) 
			);			
		}

		/**
		 * Render page
		 * 
		 * @return void
		 */
		function render_page(){
			if( isset( $_POST['do_bulk_sale'] ) ){
				include_once( $this->plugin_dir . 'pages/bulk-sale-result.php' );
			} else {
				include_once( $this->plugin_dir . 'pages/bulk-sale.php' );
			}
		}

		/**
		 * Get products
		 * 
		 * @return
		 */
		function get_products(){
			$items = get_posts( array(
				'posts_per_page' => -1,
				'post_type'	=> 'product'
			) );	

			$products = array();

			if( !empty( $items ) ){
				foreach ($items as $key => $item) {
					$product = get_product( $item->ID );

					$products[$item->ID] 				= $product;
					$products[$item->ID]->regular_price = $product->get_regular_price();
					$products[$item->ID]->sale_price 	= $product->get_sale_price();
					$products[$item->ID]->price 		= $product->get_price();
					$products[$item->ID]->is_sale 		= $product->is_on_sale();	
					$products[$item->ID]->permalink 	= get_permalink( $product->id );					
				}
			}

			return $products;
		}

		/**
		 * Get variation
		 * 
		 * @return obj
		 */
		function get_variations( $id ){
			$items = get_posts( array(
				'posts_per_page' 	=> -1,
				'post_type'			=> 'product_variation',
				'post_parent' 		=> $id
			) );	

			$products = array();

			if( !empty( $items ) ){
				foreach ($items as $key => $item) {
					$product = get_product( $item->ID );

					$products[$item->ID] 				= $product;
					$products[$item->ID]->regular_price = $product->get_regular_price();
					$products[$item->ID]->sale_price 	= $product->get_sale_price();
					$products[$item->ID]->price 		= $product->get_price();
					$products[$item->ID]->is_sale 		= $product->is_on_sale();
				}
			}

			return $products;			
		}

		/**
		 * Set product price
		 * 
		 * @param int product id
		 * @param int sale price
		 * @param int timestamp sale from
		 * @param int timestamp sale to
		 * 
		 * @return bool
		 */
		function set_product_price( $product_id = false, $sale_price = false, $sale_from = false, $sale_to = false ){

			// Feed the status
			$statuses = array();

			// Update sale price
			$update_sale_price = update_post_meta( $product_id, '_sale_price', $sale_price );

			// Push status
			array_push( $statuses, true );

			// Update sale schedule if there's given time, update _price right away if there's no data given
			if( isset( $sale_from ) && $sale_from != '' && isset( $sale_to ) && $sale_to != '' ){
				$timestamp_from = strtotime( $sale_from );
				$timestamp_to 	= strtotime( $sale_to );

				// Verify time range
				if( $timestamp_from < $timestamp_to ){
					$update_from =  update_post_meta( $product_id, '_sale_price_dates_from', $timestamp_from );
					$update_to = update_post_meta( $product_id, '_sale_price_dates_to', $timestamp_to );

					// Push statuses
					array_push( $statuses, $update_from );
					array_push( $statuses, $update_to );
				}

			} else {
				// Update the price right away
				$update_sale_price = update_post_meta( $product_id, '_price', $sale_price );

				// Push status
				array_push( $statuses, $update_sale_price );
			}		

			// Return status
			if( !in_array( false, $statuses ) ){
				return false;
			} else {
				return true;
			}
		}

	}	
	new Woocommerce_Bulk_Sale;
}