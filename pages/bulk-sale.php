<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wrap" id="bulk-sale-wrap">
	<h2><?php _e( 'Bulk Sale', 'woocommerce-bulk-sale' ); ?></h2>

	<h3><?php _e( 'A. Select Products', 'woocommerce-bulk-sale' ); ?></h3>

	<?php $this->get_product_filters(); ?>

	<form action="edit.php?post_type=product&page=woocommerce-bulk-sale" method="post" id="woocommerce-bulk-sale-form">

	<ul id="products" style="">
		<?php
			foreach ($this->get_products() as $product) {

				$price 			= wc_price( $product->price );
				$regular_price 	= wc_price( $product->regular_price );

				echo "<li style='margin: 0 0 10px 0; padding: 10px 10px 10px 35px; background: #dfdfdf; position: relative;'>";

					echo "<input type='checkbox' name='product[]' id='product-{$product->id}' value='{$product->id}' style='position: absolute; top: 18px; left: 10px;'>";

					echo "<label for='product-{$product->id}' style='display: block;'>";

					echo "<a href='{$product->permalink}'><strong class='name'>{$product->post->post_title}</strong></a> - <strong style='font-size: .8em; text-transform: uppercase;'>{$product->product_type}</strong><br />";

					if( $product->is_sale ){
						echo "<del style='color: #afafaf; display: block;' class='regular-price'>". __( 'Price :', 'woocommerce-bulk-sale' ) ."{$regular_price}</del>";
						echo "<span class='price'>". __( 'Sale Price :', 'woocommerce-bulk-sale' )  ."{$price}</span>";
					} else {
						echo "<span class='price'>". __( 'Price :', 'woocommerce-bulk-sale' ) ." {$price}</span>";
					}

					// Variable Product has more to be displayed
					if( 'variable' == $product->product_type ){

						$this->the_variations( $product->id );

					}

					echo "</label>";

				echo "</li>";

			}
		?>
	</ul>

	<?php 
		
		if( count( $this->get_products() ) >= $this->posts_per_page ) :

		// Preparing next products URL query
		$next_products_query 			= $_GET;
		$next_products_query['paged'] 	= $this->get_next_paged();
	?>
	<p id="next-products-wrap">
		<a href="<?php echo add_query_arg( $next_products_query, admin_url() . 'edit.php' ); ?>" id="next-products" class="button"><?php _e( 'Load More Products', 'woocommerce-bulk-sale' ); ?></a>
		<span id="next-products-loading" style="display: none;"><?php _e( 'Loading Products...', 'woocommerce-bulk-sale' ); ?></span>
	</p>

	<?php endif; ?>
	
	<br>
	<h3><?php _e( 'B. Set Price', 'woocommerce-bulk-sale' ); ?></h3>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="sale_type"><?php _e( 'Sale Type', 'woocommerce-bulk-sale' ); ?></label>
				</th>
				<td>
					<select name="sale_type" id="sale_type">
						<option value="fixed"><?php _e( 'Fixed Price', 'woocommerce-bulk-sale' ); ?></option>
						<option value="percentage"><?php _e( 'Decreased by Percentage Price', 'woocommerce-bulk-sale' ); ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="sale_price"><?php _e( 'Sale Price', 'woocommerce-bulk-sale' ); ?></label>
				</th>
				<td>
					<input type="number" name="sale_price" class="regular-text" placeholder="Type number of price / percentage here">
				</td>
			</tr>
		</tbody>
	</table>

	<br>
	<h3><?php _e( 'C. Set Sale Schedule (Optional)', 'woocommerce-bulk-sale' ); ?></h3>
	<table class="form-table set-sale-schedule">
		<tbody>
			<tr>
				<th scope="row">
					<label for="sale_from"><?php _e('From', 'woocommerce-bulk-sale' ) ?></label>
				</th>
				<td>
					<input type="text" name="sale_from" id="sale_from" class="regular-text set-schedule" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])(\s(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9]))?">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="sale_to"><?php _e('To', 'woocommerce-bulk-sale' ) ?></label>
				</th>
				<td>
					<input type="text" name="sale_to" id="sale_to" class="regular-text set-schedule" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])(\s(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9]))?">
				</td>
			</tr>
		</tbody>
	</table>

	<?php wp_nonce_field( 'bulk_sale' ); ?>
	<input type="hidden" name="do_bulk_sale" value="yes" />
	<input type="submit" value="<?php _e( 'Save Bulk Sale', 'woocommerce-bulk-sale' ); ?>" class="button button-primary">
</form>
</div>