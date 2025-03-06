$( document ).ready( function () {

	$( document ).on( 'click', '.sic-enable-commenting', function ( e ) {
		$( this ).find( 'a' ).text( mw.msg( 'nora-smart-comments-off' ) );
		$( this ).parent().parent().find( 'span.status' ).text( mw.msg( 'nora-smart-comments-on' ) );
	} );

	$( document ).on( 'click', '.sic-disable-commenting', function ( e ) {
		$( this ).find( 'a' ).text( mw.msg( 'nora-smart-comments-on' ) );
		$( this ).parent().parent().find( 'span.status' ).text( mw.msg( 'nora-smart-comments-off' ) );
	} );

	// Bind on 'active' state
	$( window ).on( 'sc-debug-mode', function() {
		$( 'li.smartcomments-toggler > a.navigation-link' ).find( 'span.status' ).text( mw.msg( 'nora-smart-comments-on' ) );
		$( 'li.sic-enable-commenting' ).find( 'a' ).text( mw.msg( 'nora-smart-comments-off' ) );
		$( 'li.sic-enable-commenting' ).removeClass( 'sic-enable-commenting' ).addClass( 'sic-disable-commenting' );
	} );

	$( window ).on( 'sc-comment-group-open', function() {
		setTimeout(() => {
			$( '.sc-group-main').css( 'height', document.body.scrollHeight + 'px');
		}, 10 );
	} );

	$( document ).on( 'click', '.sic-enable-commenting, .sic-disable-commenting', function( e ) {
			$( document ).trigger( 'submenus:close' );
	} );

} );
