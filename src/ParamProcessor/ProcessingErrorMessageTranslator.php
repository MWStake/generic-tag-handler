<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler;

/**
 * Translates hardcoded error messages from "DataValues" and "ParamProcessor"
 * See https://github.com/JeroenDeDauw/ParamProcessor/issues/31
 */
class ProcessingErrorMessageTranslator {

	/**
	 * @var array
	 */
	protected $messagePatterns = [
		// https://github.com/search?q=org%3ADataValues+throw+new+ParseException&type=Code
		'#The value is not recognitzed by the configured parsers#' =>
			'mwstake-components-generictaghandler-validator-value-not-recognized',
		'#Not a boolean#' => 'mwstake-components-generictaghandler-validator-invalid-boolean',
		'#Not a float#' => 'mwstake-components-generictaghandler-validator-invalid-float',
		'#Not an integer#' => 'mwstake-components-generictaghandler-validator-invalid-integer',
		'#Not a string#' => 'mwstake-components-generictaghandler-validator-invalid-string',
		'#Unable to explode coordinate segment by degree symbol \((.*?)\)#'
			=> 'mwstake-components-generictaghandler-validator-invalid-coordinate',
		'#Did not find degree symbol \((.*?)\)#' => 'mwstake-components-generictaghandler-validator-invalid-coordinate',
		'#Unable to split input into two coordinate segments#' =>
			'mwstake-components-generictaghandler-validator-invalid-coordinate',
		'#The format of the coordinate could not be determined.#' =>
			'mwstake-components-generictaghandler-validator-invalid-coordinate',
		'#The format of the coordinate could not be determined. Parsing failed.#'
			=> 'mwstake-components-generictaghandler-validator-invalid-coordinate',
		'#Not a valid geographical coordinate#' => 'mwstake-components-generictaghandler-validator-invalid-coordinate',
		'#(.*?): Unable to split string (.*?) into two coordinate segments#'
			=> 'mwstake-components-generictaghandler-validator-invalid-coordinate',

		// https://github.com/JeroenDeDauw/ParamProcessor/search?l=PHP&q=registerNewError&type=&utf8=%E2%9C%93
		"#(.*?) is not a valid parameter#" => 'mwstake-components-generictaghandler-validator-invalid-parameter',
		"#Required parameter '(.*?)' is missing#" => 'mwstake-components-generictaghandler-validator-missing-required'
	];

	/**
	 *
	 * @param string $message
	 * @return string
	 */
	public function translate( $message ) {
		foreach ( $this->messagePatterns as $pattern => $i18nKey ) {
			if ( preg_match( $pattern, $message ) !== 0 ) {
				return wfMessage( $i18nKey )->plain();
			}
		}
		return $message;
	}
}
