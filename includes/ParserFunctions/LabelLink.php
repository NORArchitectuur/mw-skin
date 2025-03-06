<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use Parser;
use Title;

/**
 * @example
 * Wiki text:
 * {{#label-link:
 * |page=Main Page
 * |label=Main Page
 * }}
 */
class LabelLink extends MustacheParserFunction {

	public const PARSER_FUNCTION_NAME = 'label-link';
	public const REQUIRED_OPTIONS = [ 'page', 'label' ];

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
			'href' => $page->getLocalURL(),
			'label' => $options['label'],
			'class' => 'label-link'
		] );
	}

}
