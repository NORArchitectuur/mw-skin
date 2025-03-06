<?php

namespace MediaWiki\Skin\NORA\ParserFunctions;

use MediaWiki\Extension\ParserFunctions\ParserFunctions;
use MWException;
use Parser;

abstract class ParserFunction extends ParserFunctions {

	public const PARSER_FUNCTION_NAME = null;
	public const REQUIRED_OPTIONS = [];
	public const ARG_OPTIONS = 'options';

	/**
	 * Binds the actual parser function to the parser
	 *
	 * @param Parser $parser
	 *
	 * @return void
	 *
	 * @throws MWException
	 */
	public static function onParserFirstCallInit( Parser $parser ): void {
		// MediaWiki expects a callable function, but running it with [ static::class, 'render' ] will not work
		// so this is a workaround to make it work.
		$parser->setFunctionHook(
			static::PARSER_FUNCTION_NAME,
			static fn ( $parser ) => call_user_func_array(
				[ static::class, 'render' ],
				self::parseArgs( func_get_args() )
			)
		);
	}

	/**
	 * @param Parser $parser
	 *
	 * @return mixed
	 */
	abstract public static function render( Parser $parser );

	/**
	 * Parses the arguments passed to the parser function
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private static function parseArgs( array $args ): array {
		// Remove the first argument, which is the parser object
		$parser = array_shift( $args );

		$args = array_filter( $args );
		$namedArgs = [];
		foreach ( $args as $arg ) {
			$arg = explode( '=', $arg );
			$namedArgs[$arg[0]] = $arg[1];
		}

		$newArgs = [ $parser ];
		if ( $namedArgs ) {
			$newArgs[self::ARG_OPTIONS] = $namedArgs;
		} else {
			$newArgs = array_merge( $newArgs, $args );
		}

		return $newArgs;
	}

}
