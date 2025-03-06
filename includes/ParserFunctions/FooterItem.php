<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use Html;
use Parser;
use Title;

/**
 * This parser function creates a footer item
 *
 * @example
 * wiki text:
 * {{#navigation-links:
 * |class=footer-quick-navigation
 * |links=[[Home|test]],[[Over NORA]],[[Actueel]],[[Contact]],[[Help]]
 * }}
 */
class FooterItem extends ParserFunction {

	public const PARSER_FUNCTION_NAME = 'footer-item';

	/**
	 * @param Parser $parser
	 * @param array $options
	 *
	 * @return array|string
	 */
	public static function render( Parser $parser, array $options = [] ) {
		$requiredParams = [ 'heading', 'contents' ];
		foreach ( $requiredParams as $param ) {
			if ( !isset( $options[$param] ) ) {
				return Html::element(
					'div',
					[ 'class' => 'error' ],
					wfMessage( 'nora-parser-function-error-parameter', basename( __CLASS__ ), $param )
				);
			}
		}

		$html = Html::openElement( 'div', [ 'class' => 'footer-top-item' ] );
		$html .= Html::element(
			'a',
			[
				'class' => 'navigation-header-item navigation-item',
				'href' => Title::newFromText( $options['heading'] )->getLocalURL()
			],
			$options['heading']
		);
		$html .= $parser->recursiveTagParseFully( $options['contents'] );
		$html .= Html::closeElement( 'div' );

		return [ $html, 'isHTML' => true ];
	}

}
