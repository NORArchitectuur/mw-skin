<?php

namespace MediaWiki\Skin\NORA\Hooks;

use InvalidArgumentException;
use MediaWiki\Skin\NORA\ParserFunctions\AvatarImage;
use MediaWiki\Skin\NORA\ParserFunctions\FooterItem;
use MediaWiki\Skin\NORA\ParserFunctions\IconLink;
use MediaWiki\Skin\NORA\ParserFunctions\LabelLink;
use MediaWiki\Skin\NORA\ParserFunctions\LinkOverview;
use MediaWiki\Skin\NORA\ParserFunctions\NavigationLinks;
use Parser;

class ParserFirstCallInit {

	/**
	 * This function will dynamically register all parser functions that are in the ParserFunctions directory.
	 *
	 * @param Parser $parser
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return void
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		AvatarImage::onParserFirstCallInit( $parser );
		FooterItem::onParserFirstCallInit( $parser );
		IconLink::onParserFirstCallInit( $parser );
		LabelLink::onParserFirstCallInit( $parser );
		LinkOverview::onParserFirstCallInit( $parser );
		NavigationLinks::onParserFirstCallInit( $parser );
	}

}
