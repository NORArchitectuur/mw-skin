<?php

// phpcs:ignoreFile -- hook names are due the 'main' hook handler

namespace MediaWiki\Skin\NORA\Hooks;

use Skin;

class SkinTemplateNavigation__Universal{

	/**
	 * Build the tool icon menu
	 *
	 * @param Skin $skinTemplate
	 * @param array $links
	 *
	 * @return void
	 */
	public static function onSkinTemplateNavigation__Universal( $skinTemplate, &$links ): void {
		$title = $skinTemplate->getRelevantTitle();
		if ( $title ) {
			self::buildToolIconMenu( $links, $skinTemplate );
		}
	}

	/**
	 * Build the tool icon menu
	 *
	 * @param Skin $skinTemplate
	 * @param $links
	 *
	 * @return void
	 */
	private static function buildToolIconMenu( &$links, Skin $skinTemplate ): void {
		$toolIcons = [ 'views.edit', 'views.formedit' ];
		if ( isset ( $links['actions']['watch'] ) ) {
			$toolIcons[] = 'actions.watch';
		}  else if ( isset ( $links['actions']['unwatch'] ) ) {
			$toolIcons[] = 'actions.unwatch';
		}
		$toolIcons[] = 'views.history';
		$skinToolIconsMenu = [];

		foreach( $toolIcons as $toolReference ) {
			$toolReference = explode( '.', $toolReference );
			$toolReferenceKey = $toolReference[0];
			$toolReferenceInfo = $toolReference[1];
			if ( isset( $links[ $toolReferenceKey ][ $toolReferenceInfo ] ) ) {
				$skinToolIconsMenu[$toolReferenceInfo] = $links[$toolReferenceKey][$toolReferenceInfo];
				$skinToolIconsMenu[$toolReferenceInfo]['class'] = 'tools-item navigation-item navigation-item-icon-only';
				$skinToolIconsMenu[$toolReferenceInfo]['link-class'] = "tool-{$toolReferenceInfo} navigation-link";
			}
		}

		foreach ( $skinToolIconsMenu as $actionName => $tool ) {
			if ( $actionName === $skinTemplate->getActionName() ) {
				$skinToolIconsMenu[$actionName]['class'] = $skinToolIconsMenu[$actionName]['class'] . ' active';
			}
		}

		$links['nora-tool-icons'] = $skinToolIconsMenu;
	}

}