$( document ).on( 'click', 'ul#contentNavigationNav li.navigation-item a.navigation-link', function ( e ) {
	e.preventDefault();
	const target = $( this ).attr( 'href' ); // e.g., "#Wat_verandert_er?"
	const targetId = target.substring( 1 ); // Remove the '#' character
	const targetElement = document.getElementById( targetId );

	if ( targetElement ) {
		const targetOffset = $( targetElement ).offset().top - ( $( 'header.navigation-bar' ).outerHeight() + 65 );

		$( this ).addClass( 'active' ).parent().siblings().find( 'a.navigation-link' ).removeClass( 'active' );

		$( 'html, body' ).animate( {
			scrollTop: targetOffset
		}, 250 );

		window.location.hash = target;
	} else {
		console.warn( `Element with ID "${targetId}" not found.` );
	}
} );
