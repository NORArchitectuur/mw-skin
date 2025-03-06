$( document ).ready( function () {

	$( '.navigation-toggle' ).on( 'click', function ( e ) {
		var $this = $( this );
		var $menu = $( '#' + $this.attr( 'aria-controls' ) );
		var expanded = $this.attr( 'aria-expanded' ) === 'true';

		$this.attr( 'aria-expanded', !expanded );
		$menu.attr( 'aria-hidden', expanded );

		if ( expanded ) {
			$menu.removeClass( 'expanded' );
			$this.focus();
		} else {
			$menu.addClass( 'expanded' );
			// If the focus is outside the menu, focus to the actual toggler,
			// since that was the referenced element for screen readers.
			$menu.find( 'a:first' ).focus().on( 'focusout', function ( e ) {
				if ( !$( e.relatedTarget ).hasClass( 'navigation-link' ) ) {
					$this.focus();
				}
			} );
		}

		e.preventDefault();
	} );

	function contentNavigation( $container ) {
		var $toggler = $container.find( '*[aria-controls]' );
		var $target = $( '#' + $toggler.attr( 'aria-controls' ) );

		$toggler.on( 'click', function ( e ) {
			var expanded = $toggler.attr( 'aria-expanded' ) === 'true';
			$toggler.attr( 'aria-expanded', !expanded );
			$target.attr( 'aria-hidden', expanded );
			e.preventDefault();
		} );
	}

	var $contentTogglers = [
		$( '.content-navigation' ),
	]

	$( window ).on( 'resize', function () {
		if ( window.innerWidth >= 768 ) {
			$.each( $contentTogglers, ( index, item ) => {
				contentNavigation( item );
			} )

			$( '.navigation-bar-navigation' )
				.removeClass( 'expanded' )
				.attr( 'aria-expanded', 'false' );
			$( '.navigation-toggle' ).attr( 'aria-expanded', 'false' );
		}
	} );

	$.each( $contentTogglers, ( index, item ) => {
		contentNavigation( item );
	} );


	$( window ).on( 'scroll', function() {
		$( 'body' ).addClass( 'page-body--scrolled' );
		if ( $( window ).scrollTop() === 0 ) {
			$( 'body' ).removeClass( 'page-body--scrolled' );
		}
	})

} );
