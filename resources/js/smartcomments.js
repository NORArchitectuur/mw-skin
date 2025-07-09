$( document ).ready( function () {

	if ( window.location.href.indexOf( 'scenabled=1' ) > -1  ) {
		$( 'li.smartcomments-toggler > a.navigation-link' ).find( 'span.status' ).text( mw.msg( 'nora-smart-comments-on' ) );
		$( 'li.sic-disable-commenting' ).find( 'a' ).text( mw.msg( 'nora-smart-comments-off' ) );
	}

	$( document ).on( 'click', '.sic-enable-commenting, .sic-disable-commenting', function ( e ) {
		$( document ).trigger( 'submenus:close' );
	} );


	if ( typeof window.SmartCommentsEventManager === 'undefined' ) {
		$( document ).on( 'click', '.sic-enable-commenting', function ( e ) {
			$( this ).find( 'a' ).text( mw.msg( 'nora-smart-comments-off' ) );
			$( this ).parent().parent().find( 'span.status' ).text( mw.msg( 'nora-smart-comments-on' ) );
			console.log( 'SmartComments: Enabled commenting' );
			e.preventDefault();
		} );

		$( document ).on( 'click', '.sic-disable-commenting', function ( e ) {
			$( this ).find( 'a' ).text( mw.msg( 'nora-smart-comments-on' ) );
			$( this ).parent().parent().find( 'span.status' ).text( mw.msg( 'nora-smart-comments-off' ) );
			console.log( 'SmartComments: Disabled commenting' );
			e.preventDefault();
		} );

		$( window ).on( 'sc-open-comment', function () {
			setTimeout( () => {
				$( '.sc-group-main' ).css( 'height', document.body.scrollHeight + 'px' );
			}, 10 );
		} );
	} else {
		window.SmartCommentsEventManager.on( 'sc-comments-enabled', function () {
			$( 'li.smartcomments-toggler > a.navigation-link' ).find( 'span.status' ).text( mw.msg( 'nora-smart-comments-on' ) );
			$( 'li.sic-disable-commenting' ).find( 'a' ).text( mw.msg( 'nora-smart-comments-off' ) );
		} )
		window.SmartCommentsEventManager.on( 'sc-comments-disabled', function () {
			$( 'li.smartcomments-toggler > a.navigation-link' ).find( 'span.status' ).text( mw.msg( 'nora-smart-comments-off' ) );
			$( 'li.sic-enable-commenting' ).find( 'a' ).text( mw.msg( 'nora-smart-comments-on' ) );
		} );
	}

} );
