/**
 * JavaScript for handling namespace choice radio buttons
 */
( function () {
	'use strict';

	/**
	 * Add event listeners to namespace choice radio buttons
	 */
	function setupNamespaceChoiceHandlers() {
		const radioButtons = document.querySelectorAll('.namespace-choice-radio');
		
		radioButtons.forEach(function(radio) {
			radio.addEventListener('change', function() {
				if (this.checked) {
					// Navigate to the URL stored in the data-href attribute
					const href = this.getAttribute('data-href');
					if (href) {
						window.location.href = href;
					}
				}
			});
		});
	}

	// When the document is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setupNamespaceChoiceHandlers);
	} else {
		setupNamespaceChoiceHandlers();
	}
}() ); 