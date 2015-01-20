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

			var date = $(this).datepicker('getDate');

			if( $(this).is('#sale_from') ){
				dates.not(this).datetimepicker( "option", "minDate", date );
			} else {
				dates.not(this).datetimepicker( "option", "maxDate", date );
			}
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
		$('#next-products').hide().remove();
		$('#next-products-loading').show();

		// Load the next products
		$.ajax({
			type : 'GET',
			url : source,
			async : false,
			success : function( response ){
				var parsed_response	 = $.parseHTML( response );
				var products 		 = $(parsed_response).find('#products');
				var next_products 	 = $(parsed_response).find('#next-products');

				// Append products
				$('#products').append( products );
				$('#next-products-wrap').prepend( next_products );

				// Hide loading state
				$('#next-products-loading').hide();
			}
		});
	} );
});