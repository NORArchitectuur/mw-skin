<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter;

use IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Page\WikiPageFactory;
use Parser;
use ParserOptions;
use ParserOutput;
use Title;
use WikiPage;

class BaseRewriter {
	/** @var WikiPageFactory */
	private $wikiPageFactory;

	/** @var Parser */
	private $parser;

	/**
	 * Constructor with dependency injection
	 *
	 * @param WikiPageFactory|null $wikiPageFactory
	 * @param Parser|null $parser
	 */
	public function __construct( ?WikiPageFactory $wikiPageFactory = null, ?Parser $parser = null ) {
		$services = MediaWikiServices::getInstance();
		$this->wikiPageFactory = $wikiPageFactory ?? $services->getWikiPageFactory();
		$this->parser = $parser ?? $services->getParser();
	}

	/**
	 * Returns the HTML of a wiki page
	 *
	 * @param Title $title The page title
	 * @param bool|string $wrapperDiv CSS class for wrapper div or true for 'noglossary'
	 * @param IContextSource|null $context Context for parser options
	 * @return string HTML content or error message
	 */
	public function getPageContents( Title $title, $wrapperDiv = true, ?IContextSource $context = null ): string {
		if ( !$title->exists() ) {
			return $this->formatPageNotFoundMessage( $title );
		}

		$wikiPage = $this->wikiPageFactory->newFromTitle( $title );
		if ( !$wikiPage->exists() ) {
			return $this->formatPageNotFoundMessage( $title );
		}

		$parserOptions = $context ? ParserOptions::newFromContext( $context ) : null;
		$parserOutput = $wikiPage->getParserOutput( $parserOptions );
		if ( !$parserOutput instanceof ParserOutput ) {
			return $this->formatPageNotFoundMessage( $title );
		}

		$attr = $this->buildParserAttributes( $wrapperDiv );
		return $parserOutput->getText( $attr );
	}

	/**
	 * Parses the given wiki text into HTML
	 *
	 * @param string $wikitext Wiki text to parse
	 * @param IContextSource $context Context for parser options
	 * @param Title|null $title Optional title, generates a unique title if null
	 * @return string Parsed HTML
	 */
	public function parseWikiTextAsHTML( string $wikitext, IContextSource $context, ?Title $title = null ): string {
		if ( $title === null ) {
			$title = Title::newFromText( "AttributeParser" . uniqid() );
		}

		$wikiPage = WikiPage::factory( $title );
		$parserOptions = ParserOptions::newFromContext( $context );

		$options = [
			'wrapperDivClass' => '',
			'enableSectionEditLinks' => false,
			'allowTOC' => false,
			'unwrap' => true
		];

		return $this->parser
			->parse( $wikitext, $wikiPage, $parserOptions )
			->getText( $options );
	}

	/**
	 * Builds parser attributes based on wrapper div setting
	 *
	 * @param bool|string $wrapperDiv
	 * @return array
	 */
	private function buildParserAttributes( $wrapperDiv ): array {
		$attr = [ 'enableSectionEditLinks' => false, 'allowTOC' => false ];

		if ( $wrapperDiv === true ) {
			$attr['wrapperDivClass'] = 'noglossary';
		} elseif ( is_string( $wrapperDiv ) ) {
			$attr['wrapperDivClass'] = $wrapperDiv;
		}

		return $attr;
	}

	/**
	 * Creates a formatted error message for non-existent pages
	 *
	 * @param Title $title
	 * @return string
	 */
	private function formatPageNotFoundMessage( Title $title ): string {
		return sprintf(
			'Required theme article <a href="%s">%s</a> does not exist',
			$title->getLocalURL(),
			$title->getFullText()
		);
	}
}
