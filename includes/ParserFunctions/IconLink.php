<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use Parser;
use Title;

/**
 * This parser function creates a link with an icon
 *
 * @example
 * wiki text:
 * {{#icon-link:
 * |page=Main Page
 * |label=Main Page
 * |icon=icon-home
 * }}
 */
class IconLink extends MustacheParserFunction {

	public const PARSER_FUNCTION_NAME = 'icon-link';
	public const REQUIRED_OPTIONS = [ 'page', 'label', 'icon' ];

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

		$page = Title::newFromText( $options['page'] );
		if ( $page === null || !$page->exists() ) {
			return self::renderErrorMessage(
				wfMessage( 'nora-parser-function-error-parameter', basename( __CLASS__ ), $options['page'] )
			);
		}

		return parent::renderWithMustacheTemplate( [
			'link-class' => 'link-alternative link-with-icon',
			'href' => $page->getLocalURL(),
			'icon' => $options['icon'],
			'label' => $options['label']
		] );
	}

}
