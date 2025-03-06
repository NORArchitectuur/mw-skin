<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter;

use ExtensionRegistry;
use MediaWiki\Skin\NORA\HTMLRewriter\Components\BreadCrumbs2;
use Title;

class Rewriter extends BaseRewriter implements RewriteFramework {

	/**
	 * @inheritDoc
	 */
	public function getModifiedTemplateData( array $data ): array {
		if (
			isset( $data['html-subtitle'] ) &&
			ExtensionRegistry::getInstance()->isLoaded( 'BreadCrumbs2' )
		) {
			$breadcrumbs = new BreadCrumbs2( [ $data['html-subtitle'] ] );
			$breadcrumbsResult = $breadcrumbs->rewrite();
			$data['html-subtitle'] = reset( $breadcrumbsResult );
		}
		return $data;
	}


	/**
	 * Replace href attributes with active class if the href matches the current page.
	 *
	 * @note this doesn't work on pages with query parameters, or non-existent pages.
	 *
	 * @param Title $title
	 * @param string $html
	 *
	 * @return string
	 */
	public function updateLinksWithActiveState( Title $title, string $html ): string {
		$localURL = $title->getLocalURL();
		$reg = '/<a\s+([^>]*?)href="([^"]+)"([^>]*)>(.*?)<\/a>/i';

		return preg_replace_callback( $reg, static function ( $matches ) use ( $localURL ) {
			$beforeHref = $matches[1];
			$href = $matches[2];
			$afterHref = $matches[3];
			$linkText = $matches[4];

			// Combine all attributes except href
			$allAttributes = trim( $beforeHref . ' ' . $afterHref );

			// Parse attributes into an associative array
			preg_match_all( '/(\w+)(?:="([^"]*)")?/', $allAttributes, $attrMatches, PREG_SET_ORDER );
			$attributes = [];
			foreach ( $attrMatches as $attr ) {
				$attrName = strtolower( $attr[1] );
				$attrValue = $attr[2] ?? '';
				$attributes[$attrName] = $attrValue;
			}

			// Modify the class attribute if href matches
			if ( strstr( $href, $localURL ) ) {
				if ( isset( $attributes['class'] ) ) {
					// Add 'active' if not already present
					$classes = explode( ' ', $attributes['class'] );
					if ( !in_array( 'active', $classes ) ) {
						$classes[] = 'active';
						$attributes['class'] = implode( ' ', $classes );
					}
				} else {
					// Add class attribute with 'active'
					$attributes['class'] = 'active';
				}
			}

			// Rebuild the attributes string
			$attrString = '';
			foreach ( $attributes as $name => $value ) {
				// Ensure proper escaping of attribute values
				$escapedValue = htmlspecialchars( $value, ENT_QUOTES );
				$attrString .= " $name=\"$escapedValue\"";
			}

			return "<a href=\"$href\"$attrString>{$linkText}</a>";
		}, $html );
	}

}
