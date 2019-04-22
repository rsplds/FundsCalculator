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

						result_data = '<div class="funds-results">';
						result_data += '<div class="result-inner result-total"> <span>Total investment</span> : <span>£ ' + response.result.total_investment + '</span></div>';
						result_data += '<div class="result-outer clearfix"><div class="result-left">';
						result_data += '<div class="result-inner result-title"> GIA </div>';
						result_data += '<div class="result-inner"> GIA Fund Charges : <span>£ ' + response.result.gia_fund_charge + '</span></div>';
						result_data += '<div class="result-inner"> GIA Exchange Trade Charges : <span>£ ' + response.result.gia_charges_et + '</span></div>';
						result_data += '<div class="result-inner"> Total GIA Charges : <span>£ ' + ( ( parseFloat(response.result.gia_charges_et))+ ( parseFloat(response.result.gia_fund_charge) ) ).toFixed(4) + '</span></div>';
						result_data += '</div><div class="result-right">';
						result_data += '<div class="result-inner result-title"> ISA </div>';
						result_data += '<div class="result-inner"> ISA Fund Charges : <span>£ ' + response.result.isa_charges_fund + '</span></div>';
						result_data += '<div class="result-inner"> ISA Exchange Charges : <span>£ ' + response.result.isa_charges_et + '</span></div>';
						result_data += '<div class="result-inner"> Total ISA Charges : <span>£ ' + ( ( parseFloat(response.result.isa_charges_et))+ ( parseFloat(response.result.isa_charges_fund) ) ).toFixed(4) + '</span></div>';
						result_data += '</div></div>';
						result_data += '<div class="result-inner result-total"><span>Total Charges</span> : <span>£ ' + response.result.total_charges + '</span></div>';
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
