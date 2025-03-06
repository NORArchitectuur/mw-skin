$( document ).ready( function () {

	const $panel = $( 'section#information-panel' );
	$panel.find( ' > .panel-footer .column.right a' ).on( 'click', function ( e ) {
		togglePanel();
		e.preventDefault();
	} );

	$panel.find( ' > .panel-footer a#contributors' ).on( 'click', function( e )  {
		togglePanel();
		e.preventDefault();
	} );

	function togglePanel() {
		$expanded = $panel.attr( 'aria-expanded' ) === 'true';
		if ( !$expanded ) {
			$panel.attr( 'aria-expanded', 'true' );
			$panel.find( ' > .panel-footer .column.right a span' ).text( mw.msg('nora-information-panel-toggle-collapse') );
		} else {
			$panel.attr( 'aria-expanded', 'false' );
			$panel.find( ' > .panel-footer .column.right a span' ).text( mw.msg('nora-information-panel-toggle-expand') );
		}
	}

} );