<?php

namespace MediaWiki\Skin\NORA\Hooks;

class SkinBuildSidebar {

	/**
	 * This function will add the active class to the sidebar link that is currently active
	 *
	 * @param \Skin $skin
	 * @param array &$bar
	 *
	 * @return void
	 */
	public static function onSkinBuildSidebar( \Skin $skin, &$bar ) {
		if ( $skin->getTitle() && $skin->getTitle()->isValid() ) {
			foreach ( $bar as $group => $links ) {
				foreach ( $links as $key => $link ) {
					if ( $link['href'] === $skin->getTitle()->getLocalURL() ) {
						$bar[$group][$key]['active'] = true;
					}
				}
			}
		}
	}

}
