<?php

if ( defined( 'MWSTAKE_MEDIAWIKI_COMPONENT_GENERICTAGHANDLER_VERSION' ) ) {
	return;
}

use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\GenericTagHandler\GenericHandler;

define( 'MWSTAKE_MEDIAWIKI_COMPONENT_GENERICTAGHANDLER_VERSION', '1.0.0' );

$GLOBALS['wgHooks']['ParserFirstCallInit'][] = static function ( Parser $parser ): bool {
	/** @var \MWStake\MediaWiki\Component\GenericTagHandler\TagFactory */
	$factory = MediaWikiServices::getInstance()->getService( 'MWStakeTagFactory' );
	$tags = $factory->getAll();
	foreach ( $tags as $tag ) {
		$genericHandler = new GenericHandler( $tag );
		$tagNames = $tag->getTagNames();
		foreach ( $tagNames as $tagName ) {
			$parser->setHook( $tagName, [ $genericHandler, 'handle' ] );
		}
	}

	return true;
};
