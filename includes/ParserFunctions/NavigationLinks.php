<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use Html;
use MediaWiki\MediaWikiServices;
use Parser;
use TitleValue;

/**
 * This parser function creates a list of navigation links
 *
 * @example
 * wiki text:
 * {{#footer-item:
 * |heading=Referentiearchitectuur
 * |contents={{#navigation-links:|class=footer-block-navigation|links=[[Grondslagen]],[[Organisatie]]
 * }}
 */
class NavigationLinks extends ParserFunction {

	public const PARSER_FUNCTION_NAME = 'navigation-links';

	/**
	 * @param Parser $parser
	 * @param array $options
	 * @return array|string
	 */
	public static function render( Parser $parser, array $options = [] ) {
		$requiredParams = [ 'class', 'links' ];
		foreach ( $requiredParams as $param ) {
			if ( !isset( $options[$param] ) ) {
				return Html::element(
					'div',
					[ 'class' => 'error' ],
					wfMessage( 'nora-parser-function-error-parameter', basename( __CLASS__ ), $param )
				);
			}
		}

		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();

		$links = explode( ',', $options['links'] );
		$html = Html::openElement( 'ul', [ 'class' => $options['class'] ] );
		foreach ( $links as $link ) {
			$link = str_replace( [ '[[', ']]' ], '', $link );
			$target = '';
			$text = null;
			if ( strstr( $link, '|' ) ) {
				$link = explode( '|', $link );
				$target = $link[0];
				$text = $link[1];
			} else {
				$target = $link;
			}

			$ns = NS_MAIN;
			if ( strstr( $target, ':' ) ) {
				$target = explode( ':', $target );
				$ns = $target[0];
				$target = $target[1];
			}

			$href = $linkRenderer->makeLink(
				new TitleValue( $ns, $target ),
				$text,
				[ 'class' => 'navigation-link' ]
			);

			$html .= Html::rawElement(
				'li',
				[ 'class' => 'navigation-item' ],
				$href
			);
		}
		$html .= Html::closeElement( 'ul' );

		return [ $html, 'isHTML' => true ];
	}

}
