<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter;

use DOMDocument;
use DOMNode;
use DOMXpath;

class RewriteComponent {

	/**
	 * @var array
	 */
	private array $componentData;

	public function __construct( array $componentData ) {
		$this->componentData = $componentData;
	}

	/**
	 * Returns the non-modified component data
	 *
	 * @return array
	 */
	public function getComponentData(): array {
		return $this->componentData;
	}

	/**
	 * Returns the rewritten component data
	 *
	 * @return array
	 */
	public function rewrite(): array {
		throw new \LogicException( "RewriteComponent::rewrite() must be implemented by a subclass" );
	}

	/**
	 * @param string $html
	 * @return DOMDocument
	 */
	public function getDOMDocument( string $html ): DOMDocument {
		$dom = new DOMDocument();
		@$dom->loadHTML(
			'<?xml encoding="utf-8" ?>' . $html,
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR
		);

		return $dom;
	}

	/**
	 * Returns the modified HTML of the given element
	 *
	 * @param string $element
	 * @param string $tag
	 * @param string $attribute
	 * @param string $value
	 *
	 * @return string
	 */
	public function modifyAttributeOfElement(
		string $element,
		string $tag,
		string $attribute,
		string $value
	): string {
		return $this->modifyAttributesOfElements(
			$this->getComponentData()[$element] ?: $element,
			[ $tag ],
			[ $attribute ],
			[ $value ]
		);
	}

	/**
	 * Returns the modified HTML of the given elements
	 *
	 * @param string $source
	 * @param array $tags
	 * @param array $attributes
	 * @param array $values
	 *
	 * @return string
	 */
	public function modifyAttributesOfElements(
		string $source,
		array $tags,
		array $attributes,
		array $values
	): string {
		$DOMDocument = $this->getDOMDocument( $source );
		$xpath = new DOMXpath( $DOMDocument );

		foreach ( $tags as $key => $tag ) {

			$tagQuery = $xpath->query( "//{$tag}" );

			/** @var DOMNode $DOMNode */
			foreach ( $tagQuery as $DOMNode ) {

				$attributeToSet = $attributes[$key];
				$valueToSet = $values[$key];

				$DOMNode->setAttribute(
					$attributeToSet,
					$DOMNode->hasAttribute( $attributeToSet )
						? $DOMNode->getAttribute( $attributeToSet ) . " " . $valueToSet
						: $valueToSet
				);

			}

		}

		return str_replace( '<?xml encoding="utf-8" ?>', '', $DOMDocument->saveHTML() );
	}

	/**
	 * Removes the given items by their id
	 *
	 * @param string $source
	 * @param array $ids
	 *
	 * @return string
	 */
	public function removeItemsById( string $source, array $ids ): string {
		$DOMDocument = $this->getDOMDocument( $source );
		$xpath = new DOMXpath( $DOMDocument );

		foreach ( $ids as $id ) {
			$tagQuery = $xpath->query( "//*[@id='{$id}']" );

			/** @var DOMNode $DOMNode */
			foreach ( $tagQuery as $DOMNode ) {
				$DOMNode->parentNode->removeChild( $DOMNode );
			}
		}

		return str_replace( '<?xml encoding="utf-8" ?>', '', $DOMDocument->saveHTML() );
	}

	/**
	 * @param DOMNode $node
	 * @param DOMDocument|null $document
	 * @return array|false|string|string[]
	 */
	public function getHTML( DOMNode $node, DOMDocument $document = null ) {
		$string = $document == null ? $this->getDOMDocument()
			->saveHTML( $node ) : $document->saveHTML( $node );
		return str_replace( '<?xml encoding="utf-8" ?>', '', $string );
	}

}
