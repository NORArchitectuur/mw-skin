<?php

namespace MediaWiki\Skin\NORA\Components;

use Html;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skin\NORA\HTMLRewriter\Rewriter;
use MediaWiki\Skin\NORA\SemanticStore;
use RequestContext;
use SMW\DIWikiPage;
use Title;
use Xml;

class InformationPanel implements IComponent {

	private Title $title;
	private DIWikiPage $diWikiPage;
	private Rewriter $rewriter;

	public const CLASS_LABEL_LINK = 'label-link';

	private const PROPERTY_AUTHOR_EMAIL = 'Mailadres';
	private const PROPERTY_AUTHOR_IMAGE = 'Profielfoto';
	private const PROPERTY_AUTHOR_NAME = 'Naam';

	public function __construct( Title $title, Rewriter $rewriter ) {
		$this->title = $title;
		$this->diWikiPage = DIWikiPage::newFromTitle( $title );
		$this->rewriter = $rewriter;
	}

	/**
	 * @inheritDoc
	 */
	public function getData(): array {
		$contributors = $this->getContributors();

		$data = [];
		$data['header'] = [
			'items' => [
				"{{project:Informationpanel/Collapsed/Links}}",
				"{{project:Informationpanel/Collapsed/Midden}}",
				"{{project:Informationpanel/Collapsed/Rechts}}",
			]
		];

		$data['columns'] = [
			'contactpersoon' => [
				'label' => wfMessage( "nora-information-panel-contactpersoon" )->text(),
				'content' => "{{project:Informationpanel/Contactpersoon}}"
			],
			'inhoudelijk_eigenaar' => [
				'label' => wfMessage( "nora-information-panel-inhoudelijk_eigenaar" )->text(),
				'content' => "{{project:Informationpanel/Inhoudelijk_eigenaar}}"
			],
			'beheerder' => [
				'label' => wfMessage( "nora-information-panel-beheerder" )->text(),
				'content' => "{{project:Informationpanel/Beheerder}}",
			],
			'aangemaakt_op' => [
				'label' => wfMessage( "nora-information-panel-aangemaakt_op" )->text(),
				'content' => "{{project:Informationpanel/Aangemaakt_op}}"
			],
			'laatste_wijziging' => [
				'label' => wfMessage( "nora-information-panel-laatste_wijziging" )->text(),
				'content' => "{{project:Informationpanel/Laatste_wijziging}}"
			],
			'bijdragers' => [
				'label' => wfMessage( "nora-information-panel-bijdragers" )->text(),
				'content' => implode( ' ', array_map( static function ( $contributor ) {
					return Html::element( 'a', [
						'href' => $contributor['href'],
						'class' => self::CLASS_LABEL_LINK
					], $contributor['name'] );
				}, $contributors ) )
			],
			'versies' => [
				'label' => wfMessage( "nora-information-panel-versies" )->text(),
				'content' => "{{project:Informationpanel/Versies}}"
			],
			'mate_van_verplichting' => [
				'label' => wfMessage( "nora-information-panel-mate_van_verplichting" )->text(),
				'content' => "{{project:Informationpanel/Mate_van_verplichting}}"
			],
			'publicatiedatum' => [
				'label' => wfMessage( "nora-information-panel-publicatiedatum" )->text(),
				'content' => "{{project:Informationpanel/Publicatiedatum}}",
			],
			'beheerregime' => [
				'label' => wfMessage( "nora-information-panel-beheerregime" )->text(),
				'content' => "{{project:Informationpanel/Beheerregime}}"
			],
			'fase' => [
				'label' => wfMessage( "nora-information-panel-fase" )->text(),
				'content' => "{{project:Informationpanel/Fase}}"
			],
		];

		$data['footer'] = [
			'contributors' => $contributors,
			'contributorCount' => count( $contributors ),
			'contributors-text' => count( $contributors ) === 1
				? wfMessage( 'nora-information-panel-bijdrager' )
				: wfMessage( 'nora-information-panel-bijdragers' )
		];

		// Run the hook to allow other extensions to modify the data
		MediaWikiServices::getInstance()->getHookContainer()->run(
			'NORA::InformationPanel',
			[ $this->diWikiPage, &$data ]
		);

		// Mustache requires that all arrays are indexed arrays, thus we need to reindex the arrays
		foreach ( $data as $section => $sectionData ) {
			if ( $section === 'header' ) {
				foreach( $sectionData['items'] as $key => $item ) {
					$data['header']['items'][ $key ] = $this->parseContentValue( [ 'content' => $item ] )['content'];
				}
			} elseif ( $section === 'columns' ) {
				$data['columns'] = array_map(
					fn ( $item ) => $this->parseContentValue( $item ),
					array_values( $data['columns'] )
				);
			}
		}

		return $data;
	}

	/**
	 * Parse the content value of an item
	 *
	 * @param array $item
	 *
	 * @return array
	 */
	private function parseContentValue( array $item ): array {
		if ( isset( $item['content'] ) && strstr( $item['content'], '{{project:' ) ) {
			$item['content'] = $this->rewriter->parseWikiTextAsHTML(
				(string)$item['content'],
				RequestContext::getMain(),
				$this->title
			);
		}
		return $item;
	}

	/**
	 * Get the contributors of a page
	 *
	 * @param int $maxContribs
	 *
	 * @return array
	 */
	private function getContributors( int $maxContribs = 5 ): array {
		$res = [];
		$i = 0;
		$propertyValues = SemanticStore::getInstance()->getPropertyValues( $this->diWikiPage, 'Page_author' );
		foreach ( $propertyValues as $contributor ) {
			if ( $i >= $maxContribs ) {
				break;
			}
			$userData = $this->getUserData( Title::newFromText( $contributor ) );
			$res[ $userData['id'] ] = $userData;
			++$i;
		}

		// We need to reindex the array to make sure it is an indexed array, since
		// mustache requires that all arrays are indexed arrays and to ensure non-duplicated values
		return array_values( $res );
	}

	/**
	 * Retrieve user data from a title
	 *
	 * @param Title $title
	 *
	 * @return array
	 */
	private function getUserData( Title $title ): array {
		$wikiDataItem = DIWikiPage::newFromTitle( $title );
		$name = SemanticStore::getInstance()->getPropertyValue( $wikiDataItem, self::PROPERTY_AUTHOR_NAME );
		if ( empty( $name ) ) {
			$name = $title->getText();
		}

		return [
			'image' => self::getImageUrl( $title ),
			'name' => $name,
			'email' => SemanticStore::getInstance()->getPropertyValue( $wikiDataItem, self::PROPERTY_AUTHOR_EMAIL ),
			'href' => $title->getLocalURL(),
			'id' => substr( md5( $title->getFullText() ), 0, 8 )
		];
	}

	/**
	 * Get the URL of an image.
	 * This function will return the URL of the image if it exists, otherwise it will create an avatar image.
	 *
	 * @param Title $title
	 *
	 * @return string
	 */
	public static function getImageUrl( Title $title ): string {
		$repo = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo();
		$imageName = SemanticStore::getInstance()->getPropertyValue(
			DIWikiPage::newFromTitle( $title ),
			self::PROPERTY_AUTHOR_IMAGE
		);

		if ( $imageName && $repo->findFile( Title::newFromText( "File:{$imageName}" ) ) ) {
			return $repo->findFile( Title::newFromText( "File:{$imageName}" ) )->getUrl();
		} else {
			return self::createAvatar( $title );
		}
	}

	/**
	 * Create an avatar image from the username
	 *
	 * @param Title $title
	 * @param int $size
	 *
	 * @return string
	 */
	public static function createAvatar( Title $title, int $size = 100 ): string {
		$userName = str_replace( '.', '', $title->getText() );
		$initials = strlen( $userName ) > 1 ? substr( $userName, 0, 2 ) : $userName;
		$numLetters = strlen( $initials );

		// Generate a hash based on the full title text for consistency
		$hash = md5( $title->getFullText() );

		// Use parts of the hash to generate consistent RGB values
		$r = hexdec( substr( $hash, 0, 2 ) );
		$g = hexdec( substr( $hash, 2, 2 ) );
		$b = hexdec( substr( $hash, 4, 2 ) );
		$bgColor = sprintf( "#%02x%02x%02x", $r, $g, $b );

		// Simple brightness check to get contrasting text color
		$brightness = ( $r * 299 + $g * 587 + $b * 114 ) / 1000;
		$textColor = ( $brightness < 128 ) ? '#FFFFFF' : '#000000';

		// For a single letter, use 60% of the avatar size; for two letters, smaller, etc.
		$maxFontSize = (int)floor( $size * 0.6 );

		// Reduce size for more letters (rough approximation)
		$fontSize = (int)floor( $maxFontSize / max( 1, $numLetters * 0.7 ) );

		// Create an SVG string
		// Note: We use text-anchor and dominant-baseline for perfect centering
		$svg = Xml::openElement( 'svg', [
			'width'       => $size,
			'height'      => $size,
			'viewBox'     => "0 0 $size $size",
			'xmlns'       => "http://www.w3.org/2000/svg",
			'role'        => "img",
			'aria-label'  => "Avatar",
		] );

		$svg .= Xml::element( 'rect', [
			'width'  => $size,
			'height' => $size,
			'fill'   => $bgColor,
		] );

		$svg .= Xml::element( 'text', [
			'x'                  => '50%',
			'y'                  => '50%',
			'fill'               => $textColor,
			'font-size'          => $fontSize,
			'font-family'        => 'Arial, sans-serif',
			'text-anchor'        => 'middle',
			'dominant-baseline'  => 'middle',
		], $initials );

		// Close SVG
		$svg .= Xml::closeElement( 'svg' );

		// Return as data URI (SVG can be URL-encoded or base64-encoded).
		return sprintf( 'data:image/svg+xml;utf8,%s', rawurlencode( $svg ) );
	}

}
