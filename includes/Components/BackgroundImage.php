<?php

namespace MediaWiki\Skin\NORA\Components;

use MediaWiki\MediaWikiServices;
use MediaWiki\Skin\NORA\HTMLRewriter\Rewriter;
use MediaWiki\Skin\NORA\SemanticStore;
use SMW\DIWikiPage;
use Title;

class BackgroundImage implements IComponent {
	private DIWikiPage $dataItem;

	private string $propertyName;

	/**
	 * @inheritDoc
	 */
	public function __construct( Title $title, Rewriter $rewriter ) {
		$this->dataItem = DIWikiPage::newFromTitle( $title );
		$this->propertyName = MediaWikiServices::getInstance()->getMainConfig()->get( 'NoraBackgroundCoverProperty' );
	}

	/**
	 * @inheritDoc
	 */
	public function getData(): array {
		$propertyValue = SemanticStore::getInstance()->getPropertyValue( $this->dataItem, $this->propertyName );
		if ( !$propertyValue ) {
			return [];
		}

		$url = strstr( $propertyValue, 'https://' ) || strstr( $propertyValue, 'http://' )
			? $propertyValue
			: $this->getImageUrl( $propertyValue );

		if ( !$url ) {
			return [];
		}

		return [
			$url
		];
	}

	/**
	 * @param string $propertyValue
	 * @return string|null
	 */
	private function getImageUrl( string $propertyValue ): ?string {
		$fileRepository = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo();
		$imageTitle = Title::newFromText( $propertyValue, NS_FILE );
		return $imageTitle && $fileRepository->findFile( $imageTitle )
			? $fileRepository->findFile( $imageTitle )->getUrl()
			: null;
	}
}
