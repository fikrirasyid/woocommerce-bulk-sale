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

		/**
		 * Init the method
		 */
		function __construct(){
			$this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
			$this->plugin_dir = plugin_dir_path( __FILE__ );

			// Enqueueing scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// Add submenu
			add_action( 'admin_menu', array( $this, 'add_page' ) );
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
					$product = new WC_Product( $item->ID );

					$products[$item->ID] 				= $product;
					$products[$item->ID]->regular_price = $product->get_regular_price();
					$products[$item->ID]->sale_price 	= $product->get_sale_price();
					$products[$item->ID]->price 		= $product->get_price();
					$products[$item->ID]->is_sale 		= $product->is_on_sale();					
				}
			}

			return $products;
		}

	}	
	new Woocommerce_Bulk_Sale;
}