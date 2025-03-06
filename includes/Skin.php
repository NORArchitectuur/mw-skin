<?php

namespace MediaWiki\Skin\NORA;

use ExtensionRegistry;
use MediaWiki\Skin\NORA\Components\BackgroundImage;
use MediaWiki\Skin\NORA\Components\InformationPanel;
use MediaWiki\Skin\NORA\HTMLRewriter\BaseRewriter;
use MediaWiki\Skin\NORA\HTMLRewriter\Components\IconOnlyPortlet;
use MediaWiki\Skin\NORA\HTMLRewriter\Components\Portlet;
use MediaWiki\Skin\NORA\HTMLRewriter\Rewriter;
use SkinMustache;
use Title;

class Skin extends SkinMustache {

	private const DEFAULT_MENU_LI_CLASS = 'navigation-item';
	private const DEFAULT_MENU_A_CLASS = 'navigation-link';

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
		$backgroundImage = new BackgroundImage( $this->getTitle(), $rewriter );

		$res = [
			'information-panel' => $informationPanel->getData()
		];

		if ( count( $backgroundImage->getData() ) >= 1 ) {
			$res['background-image'] = $backgroundImage->getData()[0];
		}

		return $res;
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
		$hideActionIds = [ 'ca-watch' ];
		return [
			'userMenu' => ( new Portlet( $data['data-portlets']['data-personal'] ) )->rewrite(),
			'contentMenus' => [
				[
					'html-items' =>
						'<li class="navigation-item info">' .
							$data['data-portlets']['data-namespaces']['label'] .
						'</li>'
				],
				( new Portlet( $data['data-portlets']['data-namespaces'] ) )->rewrite(
					self::DEFAULT_MENU_LI_CLASS,
					self::DEFAULT_MENU_A_CLASS
				),
				[
					'html-items' =>
						'<li class="navigation-item info">' .
							$data['data-portlets']['data-actions']['label'] .
						'</li>'
				],
				( new Portlet( $data['data-portlets']['data-actions'] ) )->rewrite(
					self::DEFAULT_MENU_LI_CLASS,
					self::DEFAULT_MENU_A_CLASS,
					$hideActionIds
				)
			],
			'toolMenus' => [

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
			'smartComments' => $this->msg( 'nora-smart-comments' ),
			'informationPanelToggleExpand' => $this->msg( 'nora-information-panel-toggle-expand' )
		];
	}

}
