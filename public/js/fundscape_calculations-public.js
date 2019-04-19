(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$( document ).ready( function() {

		/* Calculate Total Investment */
		calculateSum();
		$( ".fundscape-field" ).on( "keydown keyup", function() {
			calculateSum();
		} );


		/* Fetch Calculation Results */
		if( $( '#fundscape-submit' ).length > 0 ) {

			$( '#fundscape-submit' ).on( 'click', function(e) {

				e.preventDefault();
				var form_data = $( '#fundscape-form' ).serialize();

				$.ajax( {
					type: "post",
					dataType: "json",
					url: fcAjax.ajaxurl,
					data: "action=calculate_fund&"+form_data,
					beforeSend: function() {},
					success: function(response) {

						let result_data;

						result_data = '<div class="success">';
						result_data += '<div class="result-inner"> Total investment : £' + response.result.total_investment + '<div>';
						result_data += '<div class="result-inner">Total Charges : £' + response.result.total_charges + '</div>';
						result_data += '<div class="result-inner"> GIA Fund Charges : £' + response.result.gia_fund_charge + '</div>';
						result_data += '<div class="result-inner"> GIA Exchange Trade Charges : £' + response.result.gia_charges_et + '</div>';
						result_data += '<div class="result-inner"> Total Gia Charges : £' + ( ( parseFloat(response.result.gia_charges_et))+ ( parseFloat(response.result.gia_fund_charge) ) ).toFixed(2) + '</div>';
						result_data += '<div class="result-inner"> ISA Fund Charges : £' + response.result.isa_charges_fund + '</div>';
						result_data += '<div class="result-inner"> ISA Exchange Charges : £' + response.result.isa_charges_et + '</div>';
						result_data += '<div class="result-inner"> Total ISA Charges : £' + ( ( parseFloat(response.result.isa_charges_et))+ ( parseFloat(response.result.isa_charges_fund) ) ).toFixed(2) + '</div>';
						result_data += '</div>';

						$( '#show-form-result' ).html( result_data );
						$( '#show-form-result' ).show();

					},
					complete : function() {}
				} );
			} );

		}

	} );


	function calculateSum() {
		var sum = 0;

		$( ".fundscape-field" ).each( function() {

			/* add only if the value is number */
			if ( !isNaN(this.value) && this.value.length != 0 ) {
				sum += parseFloat(this.value);
				$(this).css( "background-color", "#FEFFB0" );
			} else if ( this.value.length != 0 ) {
				$(this).css( "background-color", "red" );
			}
		} );

		$( "#total_investment" ).html( sum.toFixed(2) );
	}

})( jQuery );
