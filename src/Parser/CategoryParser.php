<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler\Parser;

use Title;
use ValueParsers\ParseException;
use ValueParsers\StringValueParser;

class CategoryParser extends StringValueParser {
	/**
	 * @see StringValueParser::stringParse
	 *
	 * @param string $value
	 * @return Title
	 * @throws ParseException
	 */
	protected function stringParse( $value ) {
		if ( strpos( $value, ':' ) !== false ) {
			$title = Title::newFromText( $value );
			if ( !$title instanceof Title || $title->getNamespace() !== NS_CATEGORY ) {
				throw new ParseException(
					wfMessage(
						'mwstake-components-generictaghandler-parser-error-invalid-category-title', $value
					)->plain()
				);
			}
		} else {
			$title = Title::newFromText( $value, NS_CATEGORY );
			if ( !( $title instanceof Title ) ) {
				throw new ParseException(
					wfMessage( 'mwstake-components-generictaghandler-parser-error-invalid-title', $value )->plain()
				);
			}
		}

		return $title;
	}
}
