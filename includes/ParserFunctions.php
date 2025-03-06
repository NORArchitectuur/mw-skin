<?php

namespace MediaWiki\Skin\NORA;

use InvalidArgumentException;
use Parser;

class ParserFunctions {

	private array $handlers = [];

	public function __construct() {
		$this->registerHandlers();
	}

	/**
	 * Registers the available parser functions
	 *
	 * @param Parser $parser
	 *
	 * @return void
	 */
	public function register( Parser $parser ) {
		foreach ( $this->handlers as $parserFunctionName => $classAndMethod ) {
			$class = $classAndMethod[0];
			$method = $classAndMethod[1];

			if ( method_exists( $class, 'onParserFirstCallInit' ) ) {
				$parser->getContentLanguage()->mMagicExtensions[$parserFunctionName] = [ 0, $parserFunctionName ];
				$class::onParserFirstCallInit( $parser );
			} else {
				throw new InvalidArgumentException(
					"Parser function class $class does not have a static method onParserFirstCallInit"
				);
			}
		}
	}

	/**
	 * Registers the parser function handlers
	 *
	 * @return void
	 */
	private function registerHandlers() {
		foreach ( glob( __DIR__ . '/ParserFunctions/*.php' ) as $class ) {
			$class = __NAMESPACE__ . '\\ParserFunctions\\' . substr( basename( $class ), 0, -4 );
			$this->handlers[$class::PARSER_FUNCTION_NAME] = [ $class, 'render' ];
		}
	}

}
