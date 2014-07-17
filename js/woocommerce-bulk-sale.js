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
});