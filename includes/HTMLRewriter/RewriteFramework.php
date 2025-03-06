<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter;

interface RewriteFramework {

	/**
	 * Returns the modified template data
	 *
	 * @return array
	 */
	public function getModifiedTemplateData( array $data ): array;

}
