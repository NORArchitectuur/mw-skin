<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use Parser;
use TemplateParser;

abstract class MustacheParserFunction extends ParserFunction {

	/**
	 * Checks if the required options are set
	 *
	 * @param Parser $parser
	 * @param array $options
	 *
	 * @return array|false
	 */
	public static function hasErrors( Parser $parser, array $options = [] ) {
		foreach ( static::REQUIRED_OPTIONS as $param ) {
			if ( !isset( $options[$param] ) ) {
				return self::renderErrorMessage(
					wfMessage( 'nora-parser-function-error-parameter', basename( __CLASS__ ), $param )
				);
			}
		}
		return false;
	}

	/**
	 * Renders the parser function with its template
	 *
	 * @param array $templateData
	 * @param string|null $className
	 *
	 * @return array
	 */
	public static function renderWithMustacheTemplate( array $templateData = [], ?string $className = null ): array {
		$templateParser = new TemplateParser( __DIR__ . '/../../templates/components' );

		if ( $className === null ) {
			$className = explode( '\\', static::class );
			$className = end( $className );
		}

		return [
			// We remove all whitespace from the string to prevent MediaWiki's parser from formatting it
			// as a list.
			preg_replace( '/^[ \t]+|[\r\n]+/m', '', $templateParser->processTemplate( $className, $templateData ) ),
			'isHTML' => true
		];
	}

	/**
	 * Renders an error message
	 *
	 * @param string $errorMessage
	 *
	 * @return array
	 */
	public static function renderErrorMessage( string $errorMessage ): array {
		return self::renderWithMustacheTemplate(
			[
				'error' => $errorMessage
			],
			'ErrorMessage'
		);
	}

}
