<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter\Components;

use MediaWiki\Skin\NORA\HTMLRewriter\RewriteComponent;

class Portlet extends RewriteComponent {

	/**
	 * Rewrites the given breadcrumb data into a structured HTML list.
	 *
	 * @param string $liClass Class applied to each <li> element.
	 * @param string $aClass Class applied to each <a> element.
	 * @param array $removeIds An array containing the IDs of the items to be removed.
	 *
	 * @return array An array containing the modified HTML string.
	 *
	 * @throws \Exception If the given data is not an array or the first element is not a string.
	 */
	public function rewrite(
		string $liClass = 'navigation-item',
		string $aClass = 'navigation-link',
		array $removeIds = []
	): array {
		$submenu = $this->getComponentData();

		if ( !isset( $submenu['html-items'] ) ) {
			return $submenu;
		}

		$submenu['html-items'] = $this->modifyAttributesOfElements(
			$submenu['html-items'],
			[ 'a', 'li' ],
			[ 'class', 'class' ],
			[ $aClass, $liClass ],
		);

		if ( count( $removeIds ) > 0 ) {
			$submenu['html-items'] = $this->removeItemsById( $submenu['html-items'], $removeIds );
		}

		return $submenu;
	}
}
