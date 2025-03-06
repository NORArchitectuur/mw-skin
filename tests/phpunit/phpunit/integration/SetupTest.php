<?php

namespace MediaWiki\Skin\NORA\Tests;

use MediaWikiIntegrationTestCase;
use MediaWiki\Skin\NORA\Setup;
use MediaWiki\Skin\NORA\Components\InformationPanel;
use MediaWiki\Skin\NORA\Components\BackgroundImage;
use MediaWiki\Skin\NORA\HTMLRewriter\Rewriter;
use Title;
use SMW\DIWikiPage;
use MediaWiki\MediaWikiServices;

class SetupTest extends MediaWikiIntegrationTestCase {

	private $rewriter;
	private $title;

	protected function setUp(): void {
		parent::setUp();
		$this->rewriter = new Rewriter();
		$this->title = Title::newFromText('Test Page');
		$this->setMwGlobals( 'NoraBackgroundCoverProperty', 'NoraBackgroundCover' );
	}

	public function testCanConstruct(): void {
		$setup = new Setup();
		$this->assertTrue( true );
	}

	public function testOnExtensionFunction(): void {
		Setup::onExtensionFunction();
		$this->assertTrue( true );
	}

}

