<?php

namespace MediaWiki\Skin\NORA\Hooks;

use OutputPage;
use Skin;

class BeforePageDisplay {

	/**
	 * Add the nora-theme class to the body
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 *
	 * @return void
	 */
	public static function onBeforePageDisplay( $out, $skin ): void {
		$out->addHtmlClasses( [ 'nora-theme' ] );
	}

}
