(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

		if ( $('.platform-table').length > 0 ) {

			/* Add Funds Row on click */
			$( document ).on( 'click', '.add-funds', function() {

				let funds_count = $( '#funds_count' ).val();

				let funds_div;
				funds_div = '<div class="platform-fields-container">';
				funds_div += '<div class="platform-fields-section">Tier'+ funds_count +'</div>';
				funds_div += '<div class="platform-fields-section"><label>Bands From </label><input type="number" name="funds[' + funds_count + '][bandsfrom]" min="0" /></div>';
				funds_div += '<div class="platform-fields-section"><label>Bands To </label><input type="number" name="funds[' + funds_count + '][bandsto]" min="0" /></div>';
				funds_div += '<div class="platform-fields-section"><label>GIA </label><input type="number" name="funds[' + funds_count + '][gia]" min="0" step="any" /></div>';
				funds_div += '<div class="platform-fields-section"><label>ISA </label><input type="number" name="funds[' + funds_count + '][isa]" min="0" step="any" /></div>';
				funds_div += '<div class="platform-fields-section"><a href="javascript:" class="remove-funds button-primary dashicons-before dashicons-minus" data-count="' + funds_count + '"></a></div>';
				funds_div += '</div>';

				funds_count++;

				$( '#funds-section' ).append( funds_div );
				$( '#funds_count' ).val( funds_count );

			} );

			/* Add Exchange Trade row on click */
			$( document ).on( 'click', '.add-exchange-trades', function() {

				let et_count = $( '#et_count' ).val();

				let et_div;
				et_div = '<div class="platform-fields-container">';
				et_div += '<div class="platform-fields-section">Tier'+ et_count +'</div>';
				et_div += '<div class="platform-fields-section"><label>Bands From </label><input type="number" name="exchange_trades[' + et_count + '][bandsfrom]" min="0" /></div>';
				et_div += '<div class="platform-fields-section"><label>Bands To </label><input type="number" name="exchange_trades[' + et_count + '][bandsto]" min="0" /></div>';
				et_div += '<div class="platform-fields-section"><label>GIA </label><input type="number" name="exchange_trades[' + et_count + '][gia]" min="0" step="any" /></div>';
				et_div += '<div class="platform-fields-section"><label>ISA </label><input type="number" name="exchange_trades[' + et_count + '][isa]" min="0" step="any" /></div>';
				et_div += '<div class="platform-fields-section"><a href="javascript:" class="remove-exchange-trades button-primary dashicons-before dashicons-minus" data-count="' + et_count + '"></a></div>';
				et_div += '</div>';

				et_count++;

				$( '#exchange-trades-section' ).append( et_div );
				$( '#et_count' ).val( et_count );

			} );

			/* Remove Funds Row on click */
			$( document ).on( 'click', '.remove-funds', function() {

				let funds_count = $( '#funds_count' ).val();
				let current_fund = $( this ).attr( 'data-count' );

				let i;
				for( i = current_fund; i <= funds_count; i++ ) {
					$( 'a.remove-funds[data-count="' + i + '"]' ).closest( '.platform-fields-container' ).remove();
				}

				$( '#funds_count' ).val( current_fund );

			} );


			/* Remove Exchange Trades Row on click */
			$( document ).on( 'click', '.remove-exchange-trades', function() {

				let et_count = $( '#et_count' ).val();
				let current_et = $( this ).attr( 'data-count' );

				let i;
				for( i = current_et; i <= et_count; i++ ) {
					$( 'a.remove-exchange-trades[data-count="' + i + '"]' ).closest( '.platform-fields-container' ).remove();
				}

				$( '#et_count' ).val( current_et );

			} );

		}

	} );

})( jQuery );
