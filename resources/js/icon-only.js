$( document ).ready( function () {
	$(
		'.navigation-item-icon-only > a > span.visually-hidden, ' +
		'.navigation-bar-secondary-nav > li > a > span.visually-hidden'
	).each( function ( ) {
		$( this ).parent().attr( 'title', $( this ).text() );
		$( this ).parent().tooltip( {
			position: {
				my: "left top+5",
				at: "left bottom"
			}
		} );

	} );
} );