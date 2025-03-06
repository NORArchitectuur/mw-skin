<?php

namespace MediaWiki\Skin\NORA;

use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\Store;
use SMW\StoreFactory;
use SMWDataItem;

class SemanticStore {

	private Store $store;

	private static self $instance;

	/**
	 * SemanticStore constructor.
	 */
	public function __construct() {
		$this->store = StoreFactory::getStore();
	}

	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Returns the single value of a given property of a given page
	 *
	 * @param DIWikiPage $page
	 * @param string $property
	 * @param bool $formatValue
	 *
	 * @return string|null
	 */
	public function getPropertyValue( DIWikiPage $page, string $property, bool $formatValue = true ): ?string {
		$values = $this->getPropertyValues( $page, $property, $formatValue );
		return $values[0] ?? null;
	}

	/**
	 * Returns the property values of a given page
	 *
	 * @param DIWikiPage $page
	 * @param string $property
	 * @param bool $formatValues
	 *
	 * @return array
	 */
	public function getPropertyValues( DIWikiPage $page, string $property, bool $formatValues = true ): array {
		$property = DIProperty::newFromUserLabel( $property );
		$values = $this->store->getPropertyValues( $page, $property );
		return $formatValues ? $this->formatValues( $values ) : $values;
	}

	/**
	 * Formats the values to a string
	 *
	 * @param SMWDataItem[] $values
	 *
	 * @return array
	 */
	private function formatValues( array $values ): array {
		return array_map( fn ( SMWDataItem $value ) => $this->valueToString( $value ), $values );
	}

	/**
	 * Converts a data item to a string
	 *
	 * @param SMWDataItem $dataItem
	 *
	 * @return string|null
	 */
	private function valueToString( SMWDataItem $dataItem ): ?string {
		switch ( $dataItem->getDIType() ) {
			case SMWDataItem::TYPE_BLOB:
				return $dataItem->getString();
			case SMWDataItem::TYPE_NUMBER:
				return $dataItem->getNumber();
			case SMWDataItem::TYPE_WIKIPAGE:
				return $dataItem->getTitle()->getFullText();
			case SMWDataItem::TYPE_TIME:
				return DataValueFactory::newDataValueByItem( $dataItem, null )->getISO8601Date();
			case SMWDataItem::TYPE_URI:
				return $dataItem->getURI();
			case SMWDataItem::TYPE_BOOLEAN:
				return $dataItem->getBoolean();
			default:
				return null;
		}
	}

}
