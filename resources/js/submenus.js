$( document ).ready( function () {
	var $activeDropdown = null;
	var $withDropdown = $( '.with-dropdown' );

	$withDropdown.each( function () {
		var $toggler = $( this ).find( '[aria-controls]' );
		var $target = $( '#' + $toggler.attr( 'aria-controls' ) );

		$toggler.on( 'click', function ( e ) {
			var $parent = $( this ).parent();
			var isExpanded = $toggler.attr( 'aria-expanded' ) === 'true';

			// Close all dropdowns first if a different dropdown is open
			if ( $activeDropdown && $activeDropdown[0] !== $parent[0] ) {
				closeAllDropdowns();
			}

			// Toggle the current dropdown
			if ( isExpanded ) {
				closeDropdown( $parent, $toggler, $target );
				$activeDropdown = null;
			} else {
				openDropdown( $parent, $toggler, $target );
				$activeDropdown = $parent;
			}
			e.preventDefault();
		} );
	} );

	function closeAllDropdowns() {
		$withDropdown.each( function () {
			var $toggler = $( this ).find( '[aria-controls]' );
			var $target = $( '#' + $toggler.attr( 'aria-controls' ) );
			closeDropdown( $( this ), $toggler, $target );
		} );
		$activeDropdown = null;
	}

	function openDropdown( $parent, $toggler, $target ) {
		$parent.addClass( 'expanded' );
		$toggler.attr( 'aria-expanded', 'true' );
		$target.attr( 'aria-hidden', 'false' );
	}

	function closeDropdown( $parent, $toggler, $target ) {
		$parent.removeClass( 'expanded' );
		$toggler.attr( 'aria-expanded', 'false' );
		$target.attr( 'aria-hidden', 'true' );
	}

	$( document ).on( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && $activeDropdown ) {
			closeAllDropdowns();
		}
	} );

	$( document ).on( 'click', function ( e ) {
		if ( $activeDropdown && !$activeDropdown.has( e.target ).length ) {
			closeAllDropdowns();
		}
	} );

	$( document ).on( 'submenus:close', function () {
		if ( $activeDropdown ) {
			closeAllDropdowns();
		}
	} );

} );
