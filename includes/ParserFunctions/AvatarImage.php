<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use MediaWiki\Skin\NORA\Components\InformationPanel;
use Parser;
use Title;

/**
 * This parser function creates an avatar
 *
 * @example
 * wiki text:
 * {{#avatar:
 * |user=Yvdbogert
 * }}
 */
class AvatarImage extends MustacheParserFunction {

	public const PARSER_FUNCTION_NAME = 'avatar';
	public const REQUIRED_OPTIONS = [ 'user' ];

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

		$page = Title::newFromText( $options['user'], NS_USER );
		if ( $page === null ) {
			return self::renderErrorMessage(
				wfMessage( 'nora-parser-function-error-parameter', basename( __CLASS__ ), $options['user'] )
			);
		}

		return parent::renderWithMustacheTemplate( [
			'src' => InformationPanel::getImageUrl( $page ),
			'href' => $page->getLocalURL(),
			'name' => $options['user']
		] );
	}

}
