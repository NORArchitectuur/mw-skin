<?php

namespace MediaWiki\Skin\NORA\Components;

use MediaWiki\Skin\NORA\HTMLRewriter\Rewriter;
use Title;

interface IComponent {

	/**
	 * @param Title $title
	 * @param Rewriter $rewriter
	 */
	public function __construct( Title $title, Rewriter $rewriter );

	/**
	 * Returns an array of data that will be used to render the component in the Mustache template.
	 * @return array
	 */
	public function getData(): array;

}
