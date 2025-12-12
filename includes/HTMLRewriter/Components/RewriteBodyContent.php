<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter\Components;

use DOMElement;
use DOMXPath;
use MediaWiki\Skin\NORA\HTMLRewriter\RewriteComponent;
use Title;

class RewriteBodyContent extends RewriteComponent {

	/**
	 * Rewrites the html-body-content section to exclude the original TOC and to rewrite editsection links
	 *
	 * @return array An array containing the modified HTML string.
	 *
	 * @throws \DOMException
	 */
	public function rewrite(): array {
		$html = $this->removeItemsById( $this->getComponentData()[ 'html-body-content' ], [ 'toc' ] );
		$dom = $this->getDOMDocument( $html );
		$xpath = new DOMXPath( $dom );

		/* @var DOMElement $editSectionNode */
		foreach ( $xpath->query( "//editsection" ) as $editSectionNode ) {
			$correspondingHeader = $editSectionNode->parentNode->parentNode;

			$page = $editSectionNode->getAttribute( 'page' );
			$sectionNumber = $editSectionNode->getAttribute( 'section' );
			$sectionTitle = $editSectionNode->textContent;

			$pageTitle = Title::newFromText( $page );
			$sectionLink = $pageTitle->getLocalURL( [
				'action' => 'edit',
				'section' => $sectionNumber
			] );

			$outerSpan = $dom->createElement( 'span' );
			$outerSpan->setAttribute( 'class', 'mw-editsection' );

			$innerLink = $dom->createElement( 'a', wfMessage( 'editsection' )->text() );
			$innerLink->setAttribute( 'href', $sectionLink );
			$innerLink->setAttribute( 'title', wfMessage( 'editsectionhint', $sectionTitle )->text() );
			$outerSpan->appendChild( $innerLink );

			$editSectionNode->remove();
			$correspondingHeader->appendChild( $outerSpan );
		}

		return [ 'html-body-content' => $dom->saveHTML() ];
	}
}
