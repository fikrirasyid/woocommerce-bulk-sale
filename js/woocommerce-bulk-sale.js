jQuery(document).ready(function($) { 
	// Select all product
	$('#toggle-all-product').change(function(){
		if( $(this).is(':checked') ){
			$("input[name='product[]']").prop( 'checked', true );
		} else {
			$("input[name='product[]']").prop( 'checked', false );
		}
	});

	// Rebuilding the UX, using datetimepicker
	var dates = $( ".set-sale-schedule input" ).datetimepicker({
		defaultDate: "",
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm",
		controlType: 'select',
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			var option = $(this).is('#sale-from') ? "maxDate" : "minDate";
			var date = $(this).datepicker('getDate');
			dates.not( this ).datetimepicker( "option", option, date );
		}
	});

	$( ".date-picker" ).datetimepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm",
		numberOfMonths: 1,
	});

	$( ".date-picker-field" ).datetimepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm",
		numberOfMonths: 1,
	});	

	// Load more products
	$('#bulk-sale-wrap').on( 'click', '#next-products', function(e){
		e.preventDefault();

		// Variables
		var source = $(this).attr('href');

		// Loading state
		$('#next-products, #next-products-loading').toggle();

		// Load the next products
		$.ajax({
			type : 'GET',
			url : source,
			async : false,
			success : function( response ){
				var parsed_response = $.parseHTML( response );
				var products = $(parsed_response).find('#products');
				var next_products_url = $(parsed_response).find('#next-products').attr('href');

				// Append products
				$('#products').append( products );
				$('#next-products').attr({ 'href' : next_products_url });

				// Unloading state
				if( products.find('li').length > 0 ){
					$('#next-products, #next-products-loading').toggle();
				} else {
					$('#next-products-loading').text( 'All posts have been loaded' );
				}

			}
		});
	} );
});