/**
 * JavaScript for handling namespace choice radio buttons
 */
$( document ).ready( function () {
	'use strict';

	// Add event listeners to namespace choice radio buttons
	$( document ).on( 'change', '.namespace-choice-radio', function() {
		if ( $( this ).prop( 'checked' ) ) {
			// Navigate to the URL stored in the data-href attribute
			const href = $( this ).attr( 'data-href' );
			if ( href ) {
				window.location.href = href;
			}
		}
	});
} );