<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use Parser;

/**
 * This parser function creates a list of links with a heading.
 *
 * The parser function requires two parameters: heading and items.
 * Each link item should be in the format: [[link|anchor|description]]
 *
 * @example
 * Example wiki text:
 * {{#link-overview:
 * |heading=Verdieping
 * |items=[[Foo|bar|Who is there?]][[hoofdpagina|Hoofdpagina|Ga naar home]]
 * }}
 */
class LinkOverview extends MustacheParserFunction {

	public const PARSER_FUNCTION_NAME = 'link-overview';
	public const REQUIRED_OPTIONS = [ 'heading', 'items' ];

	/**
	 * @param Parser $parser
	 * @param array $options
	 *
	 * @return array
	 */
	public static function render( Parser $parser, array $options = [] ) {
		if ( $hasErrors = parent::hasErrors( $parser, $options ) ) {
			return $hasErrors;
		}

		$items = self::parseItems( $options['items'] );

		if ( is_string( $items ) ) {
			return self::renderErrorMessage( $items );
		}

		return parent::renderWithMustacheTemplate( [
			'heading' => $options['heading'],
			'items' => $items
		] );
	}

	/**
	 * Parses the items string into an array of structured data and validates each item.
	 * Each item is expected in the format: [[link|anchor|description]]
	 *
	 * @param string $itemsString The raw items string
	 *
	 * @return array|string
	 */
	private static function parseItems( string $itemsString ) {
		$items = [];
		$pattern = '/\[\[([^|\]]+)\|([^|\]]+)\|([^|\]]+)\]\]/'; // Matches [[link|anchor|description]]

		preg_match_all( $pattern, $itemsString, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			// Validate each item has all components
			$link = trim( $match[1] ?? '' );
			$anchor = trim( $match[2] ?? '' );
			$description = trim( $match[3] ?? '' );

			if ( empty( $link ) || empty( $anchor ) || empty( $description ) ) {
				return wfMessage( 'nora-link-overview-invalid-items-string' )->text();
			}

			$items[] = [
				'href' => $link,
				'name' => $anchor,
				'description' => $description
			];
		}

		// If no valid items are found, return an error
		if ( empty( $items ) ) {
			return wfMessage( 'nora-link-overview-invalid-items-string' )->text();
		}

		return $items;
	}

}
