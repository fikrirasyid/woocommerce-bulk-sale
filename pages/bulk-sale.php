<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wrap">
	<h2><?php _e( 'Bulk Sale', 'woocommerce-bulk-sale' ); ?></h2>

	<form action="edit.php?post_type=product&page=woocommerce-bulk-sale" method="post">

	<h3><?php _e( 'A. Select Products', 'woocommerce-bulk-sale' ); ?></h3>
	
	<ul style=" -webkit-column-count: 3; -moz-column-count: 3; column-count: 3;">
		<?php
			foreach ($this->get_products() as $product) {

				$price 			= wc_price( $product->price );
				$regular_price 	= wc_price( $product->regular_price );

				echo "<li style='margin: 0 0 10px 0; padding: 10px 10px 10px 35px; background: #dfdfdf; position: relative;'>";

					echo "<input type='checkbox' name='product[]' id='product-{$product->id}' value='{$product->id}' style='position: absolute; top: 18px; left: 10px;'>";

					echo "<label for='product-{$product->id}' style='display: block;'>";

					echo "<a href='{$product->post->guid}'><strong class='name'>{$product->post->post_title}</strong></a><br />";

					if( $product->is_sale ){
						echo "<del style='color: #afafaf; display: block;' class='regular-price'>". __( 'Price :', 'woocommerce-bulk-sale' ) ."{$regular_price}</del>";
						echo "<span class='price'>". __( 'Sale Price :', 'woocommerce-bulk-sale' )  ."{$price}</span>";
					} else {
						echo "<span class='price'>". __( 'Price :', 'woocommerce-bulk-sale' ) ." {$price}</span>";
					}

					echo "</label>";

				echo "</li>";

			}
		?>
	</ul>
	
	<br>
	<h3><?php _e( 'B. Set Price', 'woocommerce-bulk-sale' ); ?></h3>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="sale-type"><?php _e( 'Sale Type', 'woocommerce-bulk-sale' ); ?></label>
				</th>
				<td>
					<select name="sale-type" id="sale-type">
						<option value="fixed"><?php _e( 'Fixed Price', 'woocommerce-bulk-sale' ); ?></option>
						<option value="percentage"><?php _e( 'Decreased by Percentage Price', 'woocommerce-bulk-sale' ); ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="sale-price"><?php _e( 'Sale Price', 'woocommerce-bulk-sale' ); ?></label>
				</th>
				<td>
					<input type="number" name="sale-price" class="regular-text" placeholder="Type number of price / percentage here">
				</td>
			</tr>
		</tbody>
	</table>

	<br>
	<h3><?php _e( 'C. Set Sale Schedule (Optional)', 'woocommerce-bulk-sale' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="sale-from"><?php _e('From', 'woocommerce-bulk-sale' ) ?></label>
				</th>
				<td>
					<input type="text" name="sale-from" id="sale-from" class="regular-text set-schedule">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="sale-to"><?php _e('To', 'woocommerce-bulk-sale' ) ?></label>
				</th>
				<td>
					<input type="text" name="sale-to" id="sale-to" class="regular-text set-schedule">
				</td>
			</tr>
		</tbody>
	</table>

	<input type="submit" value="<?php _e( 'Save Bulk Sale', 'woocommerce-bulk-sale' ); ?>" class="button button-primary">
</form>
</div>