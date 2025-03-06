<?php

namespace MediaWiki\Skin\NORA\Hooks;

use InvalidArgumentException;
use MediaWiki\Skin\NORA\Setup;
use Parser;

class ParserFirstCallInit {

	/**
	 * This function will dynamically register all parser functions that are in the ParserFunctions directory.
	 *
	 * @param Parser $parser
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return void
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		Setup::$parserFunctions->register( $parser );
	}

}
