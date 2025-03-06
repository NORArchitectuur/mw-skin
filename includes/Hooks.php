<?php

namespace MediaWiki\Skin\NORA;

use MediaWiki\MediaWikiServices;

class Hooks {

	private array $handlers = [];

	public function __construct() {
		$this->registerHandlers();
	}

	/**
	 * Registers the required hooks of the skin with MediaWiki
	 *
	 * @return void
	 */
	public function register() {
		foreach ( $this->handlers as $hookName => $handler ) {
			MediaWikiServices::getInstance()->getHookContainer()->register( $hookName, $handler );
		}
	}

	/**
	 * Registers the actual hook handlers
	 *
	 * @return void
	 */
	private function registerHandlers() {
		foreach ( glob( __DIR__ . '/Hooks/*.php' ) as $hookFileName ) {
			$hookName = basename( $hookFileName, '.php' );
			$hookCaller = sprintf( 'on%s', $hookName );
			$hookBinder = str_replace( '_', ':', $hookName );
			$hookClass = __NAMESPACE__ . '\\Hooks\\' . basename( $hookFileName, '.php' );
			$this->handlers[$hookBinder][] = [ $hookClass, $hookCaller ];
		}
	}

}
