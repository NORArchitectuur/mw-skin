<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter\Components;

use DOMXPath;
use Html;
use MediaWiki\Skin\NORA\HTMLRewriter\RewriteComponent;

class BreadCrumbs2 extends RewriteComponent {

	/**
	 * Rewrites the given breadcrumb data into a structured HTML list.
	 *
	 * @param string $ulClass Class applied to the <ul> element.
	 * @param string $liClass Class applied to each <li> element.
	 * @param string $aClass Class applied to each <a> element.
	 *
	 * @return array An array containing the modified HTML string.
	 *
	 * @throws \Exception If the given data is not an array or the first element is not a string.
	 */
	public function rewrite(
		string $ulClass = 'breadcrumbs',
		string $liClass = 'breadcrumb-item',
		string $aClass = 'breadcrumb-link'
	): array {
		$data = $this->getComponentData();

		// Expecting $data to contain at least one HTML string of breadcrumbs
		if ( empty( $data[0] ) ) {
			return $data;
		}

		$html = $data[0];

		// Initialize the DOM and XPath
		$dom = $this->getDOMDocument( $html );
		$xpath = new DOMXPath( $dom );

		// Prepare the base HTML structure
		$htmlParts = [];
		$htmlParts[] = Html::openElement( 'div', [ 'id' => 'breadcrumbs2' ] );
		$htmlParts[] = Html::openElement( 'ul', [ 'class' => $ulClass ] );

		// Create a base <li> element to clone for each breadcrumb
		$baseLi = $dom->createElement( 'li' );
		if ( $liClass !== '' ) {
			$baseLi->setAttribute( 'class', $liClass );
		}

		// Process <a> tags as breadcrumbs
		$anchorNodes = $xpath->query( '//a' );
		foreach ( $anchorNodes as $index => $anchorNode ) {
			$liClone = $baseLi->cloneNode();

			// If it's the last link, adjust the class
			if ( $index === ( $anchorNodes->length - 1 ) ) {
				$finalClass = trim( 'breadcrumbs2-no-arrow ' . $liClass );
				$liClone->setAttribute( 'class', $finalClass );
			}

			// Append $aClass to the <a> if provided
			if ( $aClass !== '' ) {
				$currentAnchorClass = $anchorNode->getAttribute( 'class' );
				$anchorNode->setAttribute( 'class', trim( $currentAnchorClass . ' ' . $aClass ) );
			}

			$liClone->appendChild( $anchorNode );
			$htmlParts[] = $dom->saveHTML( $liClone );
		}

		// Process the current title if present
		$currentTitleNodes = $xpath->query( '//span[@id="breadcrumbs2-currentitle"]' );
		if ( $currentTitleNodes->length > 0 ) {
			$currentTitleNode = $currentTitleNodes->item( 0 );
			$liClone = $baseLi->cloneNode();

			// Create a dummy link for the current title
			$titleLink = $dom->createElement( 'a' );
			$textNode = $dom->createTextNode( (string)$currentTitleNode->textContent );
			$titleLink->appendChild( $textNode );
			if ( $aClass !== '' ) {
				$titleLink->setAttribute( 'class', $aClass );
			}
			// Assuming the current title doesn't have an actual URL
			$titleLink->setAttribute( 'href', '' );

			// Replace the original span with our link in the DOM
			$currentTitleNode->parentNode->replaceChild( $titleLink, $currentTitleNode );

			$liClone->appendChild( $titleLink );
			$htmlParts[] = $dom->saveHTML( $liClone );
		}

		// Close the HTML structure
		$htmlParts[] = Html::closeElement( 'ul' );
		$htmlParts[] = Html::closeElement( 'div' );

		return [ implode( '', $htmlParts ) ];
	}
}
