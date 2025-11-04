<?php

namespace MediaWiki\Skin\NORA;

use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skin\NORA\Components\BackgroundImage;
use MediaWiki\Skin\NORA\Components\InformationPanel;
use MediaWiki\Skin\NORA\HTMLRewriter\BaseRewriter;
use MediaWiki\Skin\NORA\HTMLRewriter\Components\IconOnlyPortlet;
use MediaWiki\Skin\NORA\HTMLRewriter\Components\Portlet;
use MediaWiki\Skin\NORA\HTMLRewriter\Components\PortletChoice;
use MediaWiki\Skin\NORA\HTMLRewriter\Rewriter;
use SkinMustache;
use Title;

class Skin extends SkinMustache {

	private const DEFAULT_MENU_LI_CLASS = 'navigation-item';
	private const DEFAULT_MENU_A_CLASS = 'navigation-link';

	public function __construct( $options = null ) {
		if ( version_compare( FARM_VERSION, '1.43', '>=' ) ) {
			// templateDirectory was overwritten from MW1.43
			// @note https://app.asana.com/1/565132877083/project/1209235036366813/task/1210725502434402?focus=true
			$options[ "templateDirectory" ] = "skins/NORA/templates";
		}

		parent::__construct( $options );
	}

	public function getTemplateData(): array {
		$data = parent::getTemplateData();
		$rewriter = new Rewriter();
		$data[ 'user' ] = [
			'isRegistered' => $this->getUser()->isRegistered(),
			'name' => $this->getUser()->getName(),
			'loginLink' => Title::newFromText( 'UserLogin', NS_SPECIAL )->getLocalURL(),
		];
		$data['skip-links'] = $this->buildSkipLinks();
		$data['main-navigation'] = $this->buildSecondaryNavigation();
		$data['footer'] = $this->buildFooterData( $rewriter );
		$data['current-action'] = $this->getActionName();
		$data['is-view-action'] = $this->getActionName() === 'view' && $this->getTitle() && !$this->getTitle()->isTalkPage();
		if ( $this->getTitle() ) {
			$data['view-page-url'] = $this->getTitle()->isTalkPage()
				? $this->getTitle()->getSubjectPage()->getLocalURL()
				: $this->getTitle()->getLocalURL();
		}
		$data['portletsModified'] = $this->buildPortletData( $data, $rewriter );
		$data['skinMessages'] = $this->getSkinMessages();
		$data['has-smartcomments'] = self::hasSmartComments();
		$data['user']['login-text'] = $this->msg( 'pt-login' );
		$data['sidebar'] = parent::buildSidebar();

		if ( self::hasSmartComments() ) {
			$this->getOutput()->addModules( 'skins.nora.smartcomments' );
		}

		if (
			$this->getTitle() &&
			$this->getTitle()->isValid() &&
			!$this->getTitle()->isSpecialPage() &&
			$this->getActionName() === 'view' &&
			$this->getTitle()->exists()
		) {
			$data['components'] = $this->getSkinComponents( $rewriter );
		}

		// Set has-sidebar flag if either TOC or toc-image exists
		$data['has-sidebar'] = !empty( $data['data-toc'] ) || !empty( $data['components']['toc-image'] );

		return $rewriter->getModifiedTemplateData( $data );
	}

	/**
	 * Check if the SmartComments extension is loaded
	 *
	 * @return bool
	 */
	public static function hasSmartComments(): bool {
		return ExtensionRegistry::getInstance()->isLoaded( 'SmartComments' );
	}

	/**
	 * Get the skin components for the Mustache template
	 *
	 * @param Rewriter $rewriter
	 *
	 * @return array
	 */
	private function getSkinComponents( Rewriter $rewriter ): array {
		$informationPanel = new InformationPanel( $this->getTitle(), $rewriter );
		$coverImage = new BackgroundImage( $this->getTitle(), $rewriter );
		$tocImage = new BackgroundImage( $this->getTitle(), $rewriter, 'NoraTocImageProperty' );

		$res = [
			'information-panel' => $informationPanel->getData()
		];

		if ( count( $coverImage->getData() ) >= 1 ) {
			$res['background-image'] = $coverImage->getData()[0];
		}

		if ( count( $tocImage->getData() ) >= 1 ) {
			$data = $tocImage->getData();
			$res['toc-image'] = $data[0];
			$res['toc-alt'] = $data[1];
			$res['toc-image-url'] = $tocImage->getPropertyValue();
		}

		return $res;
	}

	/**
	 * Process the talk link HTML to add navigation classes and spacing
	 *
	 * @param string $htmlItems The HTML items from the namespaces portlet
	 *
	 * @return array|null Array with 'html-items' key containing modified HTML, or null if no talk link found
	 */
	private function buildTalkLinkHtml( string $htmlItems ): ?array {
		// Parse HTML to find and modify the talk link
		$dom = new \DOMDocument();
		@$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $htmlItems, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		$xpath = new \DOMXPath( $dom );

		// Find the <li> with id="ca-talk"
		$talkItem = $xpath->query( '//li[@id="ca-talk"]' )->item( 0 );

		if ( !$talkItem ) {
			return null;
		}

		// Add navigation-item class to <li>
		$liClass = $talkItem->getAttribute( 'class' );
		$talkItem->setAttribute( 'class', trim( $liClass . ' navigation-item' ) );

		// Get the <a> tag inside
		$linkNode = $xpath->query( './/a', $talkItem )->item( 0 );
		if ( $linkNode ) {
			// Add navigation-link class to <a>
			$aClass = $linkNode->getAttribute( 'class' );
			$linkNode->setAttribute( 'class', trim( $aClass . ' navigation-link' ) );

			// Add space after icon
			$space = $dom->createTextNode( ' ' );
			$linkNode->insertBefore( $space, $linkNode->firstChild->nextSibling );
		}

		// Get the modified HTML
		$modifiedHtml = str_replace( '<?xml encoding="utf-8" ?>', '', $dom->saveHTML( $talkItem ) );

		return [ 'html-items' => $modifiedHtml ];
	}

	/**
	 * Returns the portlet data for the Mustache template
	 *
	 * @param array $data
	 * @param BaseRewriter $rewriter
	 *
	 * @return array
	 *
	 * @throws \Exception
	 */
	private function buildPortletData( array $data, BaseRewriter $rewriter ): array {
		$hideActionIds = [ 'ca-watch', 'ca-unwatch' ];
		$isViewAction = $this->getActionName() === 'view';

		$contentMenus = [];

		// Only show namespace link on view action
		if ( $isViewAction && isset( $data['data-portlets']['data-namespaces']['html-items'] ) ) {
			$talkLinkHtml = $this->buildTalkLinkHtml( $data['data-portlets']['data-namespaces']['html-items'] );
			if ( $talkLinkHtml !== null ) {
				$contentMenus[] = $talkLinkHtml;
			}
		}

		// Always show actions (without label)
		$contentMenus[] = ( new Portlet( $data['data-portlets']['data-actions'] ) )->rewrite(
			self::DEFAULT_MENU_LI_CLASS,
			self::DEFAULT_MENU_A_CLASS,
			$hideActionIds
		);

		return [
			'userMenu' => ( new Portlet( $data['data-portlets']['data-personal'] ) )->rewrite(),
			'contentMenus' => $contentMenus,
			'toolMenus' => [
				( new Portlet( $data['data-portlets-sidebar']['array-portlets-rest'][0] ?? [] ) )->rewrite(
					self::DEFAULT_MENU_LI_CLASS,
					self::DEFAULT_MENU_A_CLASS,
					[
						't-recentchangeslinked',
						't-print'
					]
				)
			],
			'quickToolbar' => ( new IconOnlyPortlet( $data['data-portlets']['data-nora-tool-icons'] ) )->rewrite()
		];
	}

	/**
	 * Build the main navigation data. This data will be passed to the mustache template.
	 *
	 * @return array[]
	 */
	private function buildSecondaryNavigation(): array {
		return [
			'help' => [
				'text' => $this->msg( 'nora-help' ),
				'link' => Title::newFromText( $this->getConfig()->get( 'NoraHelpTitle' ) )->getLocalURL(),
				'active' =>
					$this->getTitle() &&
					$this->getTitle()->exists() &&
					$this->getTitle()->equals( Title::newFromText( $this->getConfig()->get( 'NoraHelpTitle' ) ) )
			]
		];
	}

	/**
	 * Build the footer data. This data will be passed to the mustache template.
	 *
	 * @param Rewriter $rewriter
	 *
	 * @return array
	 */
	private function buildFooterData( Rewriter $rewriter ): array {
		return [
			'top' => $rewriter->updateLinksWithActiveState(
				$this->getTitle(),
				$rewriter->getPageContents(
					Title::newFromText( 'Project:FooterTop' ),
					'noglossary footer-container-top'
				)
			),
			'bottom' => [
				'left' => $rewriter->updateLinksWithActiveState(
					$this->getTitle(),
					$rewriter->getPageContents( Title::newFromText( 'Project:FooterBottom/Left' ) )
				),
				'right' => $rewriter->updateLinksWithActiveState(
					$this->getTitle(),
					$rewriter->getPageContents( Title::newFromText( 'Project:FooterBottom/Right' ) )
				)
			]
		];
	}

	/**
	 * Build the skip links data. This data will be passed to the mustache template.
	 *
	 * @return array[]
	 */
	private function buildSkipLinks(): array {
		return [
			[
				'text' => $this->msg( 'nora-skiplinks-to-content' ),
				'href' => '#content'
			],
			[
				'text' => $this->msg( 'nora-skiplinks-to-navigation' ),
				'href' => '#mainNavigation'
			]
		];
	}

	/**
	 * Returns an array of skin messages.
	 *
	 * @return array
	 */
	private function getSkinMessages(): array {
		return [
			'toggleMenu' => $this->msg( 'nora-toggle-menu' ),
			'tocHeading' => $this->msg( 'nora-toc-heading' ),
			'userMenuAltText' => $this->msg( 'nora-user-menu-text' ),
			'helpText' => $this->msg( 'nora-menu-help' ),
			'contentMenu' => $this->msg( 'nora-page-actions' ),
			'toolsMenu' => $this->msg( 'nora-tools-menu' ),
			'smartComments' => $this->msg( 'nora-smart-comments' ),
			'informationPanelToggleExpand' => $this->msg( 'nora-information-panel-toggle-expand' )
		];
	}

	/**
	 * Temporary hotfix for 1.43
	 * @param $desc
	 * @param $page
	 * @return string|void
	 */
	public function footerLink( $desc, $page ) {
		if ( !$this->getTitle() ) {
			return;
		}

		return \Html::element( 'a',
			[ 'href' => $this->getTitle()->fixSpecialName()->getLinkURL() ],
			$this->msg( $desc )->text()
		);
	}

}
