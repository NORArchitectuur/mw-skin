<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter\Components;

use DOMXPath;
use MediaWiki\Skin\NORA\HTMLRewriter\RewriteComponent;

class IconOnlyPortlet extends RewriteComponent {

	public function rewrite(): array {
		$portlet = $this->getComponentData();

		// If 'html-items' is not set, just return the original data
		if ( !isset( $portlet['html-items'] ) ) {
			return $portlet;
		}

		$dom = $this->getDOMDocument( $portlet['html-items'] );
		$xpath = new DOMXPath( $dom );
		
		foreach ( $xpath->query( "//a" ) as $item ) {
			// Remove unwanted attributes
			$item->removeAttribute( 'title' );
			$item->removeAttribute( 'accesskey' );

			// Grab the existing text content (e.g. "Bewerken", "Volgen", etc.)
			$originalText = trim( $item->textContent );

			// Grab the full class attribute (e.g. "tool-edit navigation-link")
			$classAttr = $item->hasAttribute( 'class' ) ? $item->getAttribute( 'class' ) : '';

			// Find the first 'tool-xxx' substring if it exists
			// This RegEx looks for "tool-" followed by any sequence of non-whitespace chars
			$toolType = null;
			if ( preg_match( '/\btool-([^\s]+)/', $classAttr, $matches ) ) {
				// $matches[1] will be the substring after "tool-", e.g. "edit" or "watch" or "history"
				$toolType = $matches[1];
			}

			// Remove all existing child nodes (including the old text)
			while ( $item->hasChildNodes() ) {
				$item->removeChild( $item->firstChild );
			}

			if ( !empty( $originalText ) ) {
				$span = $dom->createElement( 'span', $originalText );
				$span->setAttribute( 'class', 'visually-hidden' );
				$item->appendChild( $span );
			}

			if ( $toolType !== null ) {
				$icon = $dom->createElement( 'i' );
				$icon->setAttribute( 'class', 'ti ti-' . $toolType );
				$item->appendChild( $icon );
			}

		}

		$portlet['html-items'] = str_replace( '<?xml encoding="utf-8" ?>', '', $dom->saveHTML() );

		return $portlet;
	}
}
