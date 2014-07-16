jQuery(document).ready(function($) { 
	// Rebuilding the UX, using datetimepicker
	var dates = $( ".set-sale-schedule input" ).datetimepicker({
		defaultDate: "",
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm",
		controlType: 'select',
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			var option = $(this).is('#sale-from') ? "minDate" : "maxDate";
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