$( document ).ready( function () {
	$(
		'.navigation-item-icon-only > a > span.visually-hidden, ' +
		'.navigation-bar-secondary-nav > li > a > span.visually-hidden'
	).each( function ( ) {
		$( this ).parent().attr( 'title', $( this ).text() );
		$( this ).parent().tooltip( {
			position: {
				my: "center top+4",
				at: "center bottom", 
				collision: "fit" 
			},
			show: false,
			hide: false,
			tooltipClass: "nora-tooltip",
		} );

	} );
} );