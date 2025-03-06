<?php

namespace MediaWiki\Skin\NORA;

class Setup {

	private Hooks $hooks;
	public static ParserFunctions $parserFunctions;

	/**
	 * Setup constructor.
	 */
	public function __construct() {
		$this->hooks = new Hooks();
		self::$parserFunctions = new ParserFunctions();
	}

	/**
	 * Register the skin's parser hooks
	 *
	 * @return void
	 */
	public static function onExtensionFunction(): void {
		$setup = new self();
		$setup->registerHooks();
	}

	/**
	 * Register the extension hooks
	 *
	 * @return void
	 */
	private function registerHooks(): void {
		$this->hooks->register();
	}

}
