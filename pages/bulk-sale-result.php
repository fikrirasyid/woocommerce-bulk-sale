<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wrap">
	<h2><?php _e( 'Processing Bulk Sale', 'woocommerce-bulk-sale' ); ?></h2>

	<?php 
		// Verify action
		if( !isset( $_POST['_wpnonce'] ) || !wp_verify_nonce( $_POST['_wpnonce'], 'bulk_sale' ) ){

			echo '<p><strong>';

			_e( 'You are not authorized to perform this action', 'woocommerce-bulk-sale' );

			echo '</strong></p>';

		} elseif( !isset( $_POST['product'] ) || empty( $_POST['product']) ) {

			echo '<p><strong>';

			_e( 'No action performed. You have to select at least one product to be updated.', 'woocommerce-bulk-sale' );

			echo '</strong></p>';

		} else {

			// Thou shalt provide sale price
			if( !isset( $_POST['sale_price'] ) || $_POST['sale_price'] == '' )
				return new WP_Error( 403, __( 'No editing performed. Please provide the sale price', 'woocommerce-bulk-sale' ) );

			switch ( $_POST['sale_type'] ) {
				case 'percentage':
					$type = 'percentage';

					$multiplier = intval( $_POST['sale_price'] ) / 100;
					break;
				
				default:
					// fixed
					$type 		= 'fixed';
					
					$sale_price = intval( $_POST['sale_price'] );
					break;
			}

			// loop and edit the product sale price
			foreach ( $_POST['product'] as $product_id ) {

				$product_id = intval( $product_id );

				$product_old = get_product( intval(  $product_id ) );
					
				$price = intval( $product_old->get_regular_price() );

				// Set sale price
				if( 'percentage' == $type ){
					
					$sale_price = $price - ( $price * $multiplier );

					// Just in case the result is smaller than zero
					if( 0 > $sale_price ){
						$sale_price = 0;
					}
				} else {
					$sale_price = intval( $_POST['sale_price'] );
				}

				// Update the price
				$this->set_product_price( $product_id, $price, $sale_price, $_POST['sale_from'], $_POST['sale_to'] );

				// More actions for variable product
				if( 'variable' == $product_old->product_type ){

					$variations = $this->get_variations( $product_id );

					// Loop the variable and update its data
					if( !empty( $variations ) ){

						foreach ( $variations as $variation ) {
								
							$price = intval( $variation->get_regular_price() );

							// Set sale price
							if( 'percentage' == $type ){
								
								$sale_price = $price - ( $price * $multiplier );

								// Just in case the result is smaller than zero
								if( 0 > $sale_price ){
									$sale_price = 0;
								}
							}					

							$update_variation = $this->set_product_price( $variation->variation_id, $price, $sale_price, $_POST['sale_from'], $_POST['sale_to'] );

						}
					}
				}
			}

			// Update the sale data
			wc_scheduled_sales();

			// display time notification
			if( isset( $_POST['sale_from'] ) && $_POST['sale_from'] != '' ){
				$timestamp_from = strtotime( $_POST['sale_from'] );

				// Notice the time change
				if( $timestamp_from > current_time( 'timestamp' ) ){

					echo "<h3>";
					
					printf( __( 'The price does not changed now. The sale is scheduled to happen between %s to %s', 'woocommerce-bulk-sale' ), $_POST['sale_from'], $_POST['sale_to'] );

					echo "</h3>";

				}
			}			

			// Display the result
			echo "<ul>";

			foreach ( $_POST['product'] as $product_id ) {

				// Preview result
				$product 		= get_product( $product_id );
				$price 			= wc_price( $product->price );
				$regular_price 	= wc_price( $product->regular_price );
				$permalink 		= get_permalink( $product_id );

				echo "<li style='margin: 0 0 10px 0; padding: 10px; background: #dfdfdf; position: relative;'>";

					echo "<a href='{$permalink}'><strong class='name'>{$product->post->post_title}</strong></a><br />";

					if( $product->is_on_sale() ){
						echo "<del style='color: #afafaf; display: block;' class='regular-price'>". __( 'Price :', 'woocommerce-bulk-sale' ) ."{$regular_price}</del>";
						echo "<span class='price'>". __( 'Sale Price :', 'woocommerce-bulk-sale' )  ."{$price}</span>";
					} else {
						echo "<span class='price'>". __( 'Price :', 'woocommerce-bulk-sale' ) ." {$price}</span>";
					}

					// Display variable data
					if( 'variable' == $product->product_type ){

						$this->the_variations( $product->id );					
						
					}

				echo "</li>";	

			}

			echo "</ul>";
		}

	?>
	<p><a href="<?php echo admin_url(); ?>edit.php?post_type=product&page=woocommerce-bulk-sale" title="<?php _e( 'Edit More Product', 'woocommerce-bulk-sale' ); ?>" ><?php _e( 'Edit More Product', 'woocommerce-bulk-sale' ); ?></a></p>
</div>