<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler\Parser;

use Title;
use ValueParsers\StringValueParser;

class TitleParser extends StringValueParser {
	/**
	 * @see StringValueParser::stringParse
	 *
	 * @param string $value
	 * @return Title
	 * @throws ParseException
	 */
	protected function stringParse( $value ) {
		$oTitle = Title::newFromText( $value );

		if ( $oTitle instanceof Title === false ) {
			return null;
		}

		return $oTitle;
	}
}
