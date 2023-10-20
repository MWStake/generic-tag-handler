<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler\Parser;

use MediaWiki\MediaWikiServices;
use ValueParsers\ParseException;
use ValueParsers\StringValueParser;

class NamespaceParser extends StringValueParser {

	/**
	 *
	 * @param string $value
	 * @return int
	 */
	protected function stringParse( $value ) {
		if ( is_int( $value ) ) {
			return $value;
		}
		$namespaceInfo = MediaWikiServices::getInstance()->getNamespaceInfo();
		$nsId = $namespaceInfo->getCanonicalIndex( $value );
		if ( $nsId === false ) {
			throw new ParseException(
				wfMessage( 'mwstake-components-generictaghandler-parser-error-invalid-namespace', $value )->plain()
			);
		}

		return $nsId;
	}
}
