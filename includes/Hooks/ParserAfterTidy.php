<?php

namespace MediaWiki\Skin\NORA\Hooks;

use DOMDocument;
use DOMXPath;
use Parser;

class ParserAfterTidy {

	/**
	 * Adds the wikitable-container--overflow-inline table wrapper to tables
	 *
	 * @param Parser &$parser
	 * @param string &$text
	 *
	 * @return void
	 */
	public static function onParserAfterTidy( &$parser, &$text ): bool {
		$text = '<?xml encoding="UTF-8">' . $text;

		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$dom->encoding = 'UTF-8';

		@$dom->loadHTML( $text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING );

		$xpath = new DOMXPath( $dom );
		$tables = $xpath->query( '//table[not(ancestor::table)]' );

		foreach ( $tables as $table ) {
			$wrapper = $dom->createElement( 'div' );
			$wrapper->setAttribute( 'class', 'wikitable-container--overflow-inline' );
			$table->parentNode->insertBefore( $wrapper, $table );
			$wrapper->appendChild( $table );
		}

		$text = $dom->saveHTML();
		$text = preg_replace( '/<\?xml encoding="UTF-8"\>/', '', $text );

		return true;
	}

}
