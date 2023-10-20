<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler;

use Exception;
use MediaWiki\Html\Html;
use ParamProcessor\Options;
use ParamProcessor\Processor;

class GenericHandler {

	public const TAG_DIV = 'div';
	public const TAG_SPAN = 'span';
	public const TAG_BUTTON = 'button';

	/**
	 *
	 * @return array
	 */
	protected function getValidElementNames() {
		return [
			static::TAG_BUTTON,
			static::TAG_DIV,
			static::TAG_SPAN
		];
	}

	/**
	 *
	 * @var ITag
	 */
	protected $tag = null;

	/**
	 *
	 * @var string[]
	 */
	protected $errors = [];

	/**
	 *
	 * @var string
	 */
	protected $input = '';

	/**
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 *
	 * @var \Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @var mixed
	 */
	protected $processedInput = '';

	/**
	 *
	 * @var array
	 */
	protected $processedArgs = [];

	/**
	 *
	 * @var \PPFrame
	 */
	protected $frame = null;

	/**
	 *
	 * @param ITag $tag
	 */
	public function __construct( $tag ) {
		$this->tag = $tag;
	}

	/**
	 *
	 * @param string $input
	 * @param array $args
	 * @param \Parser $parser
	 * @param \PPFrame $frame
	 * @return array
	 * @throws \MWException
	 */
	public function handle( $input, array $args, \Parser $parser, \PPFrame $frame ) {
		$elementName = $this->tag->getContainerElementName();
		if ( $elementName !== "" && !$this->isValidContainerElementName( $elementName ) ) {
			$tagNames = $this->tag->getTagNames();
			throw new Exception(
				"Invalid container element name for tag '{$tagNames[0]}'!"
			);
		}

		$this->parser = $parser;
		$this->input = $input;
		$this->args = $args;
		$this->frame = $frame;

		$this->processInput();
		$this->processArgs();

		if ( $this->hasErrors() ) {
			return $this->makeErrorOutput();
		}

		if ( $this->tag->needsDisabledParserCache() ) {
			$this->parser->getOutput()->updateCacheExpiry( 0 );
		}

		$this->addResourceLoaderModules();

		$handler = $this->tag->getHandler(
			$this->processedInput,
			$this->processedArgs,
			$parser,
			$frame
		);

		try {
			$output = $handler->handle();
		} catch ( Exception $ex ) {
			// TODO: Find way to output "hidden" trace
			$this->errors[] = $ex->getMessage();
			return $this->makeErrorOutput();
		}

		if ( $elementName !== "" ) {
			$output = Html::rawElement(
				$elementName,
				$this->makeContainerAttributes(),
				$output
			);
		}

		return [
			$output,
			MarkerType::KEY => (string)$this->tag->getMarkerType()
		];
	}

	/**
	 * @return array
	 */
	protected function makeContainerAttributes() {
		$cssClasses = [ 'mwstake-components-generictaghandler-tag' ];
		foreach ( $this->tag->getTagNames() as $tagName ) {
			$cssClasses[] = \Sanitizer::escapeClass( "mwstake-components-generictaghandler-tag-$tagName" );
		}

		$attribs = [
			'class' => implode( ' ', array_unique( $cssClasses ) )
		];

		foreach ( $this->args as $argName => $argValue ) {
			$attribs["data-mwstake-components-generictaghandler-arg-$argName"] = $argValue;
		}

		$attribs["data-mwstake-components-generictaghandler-input"] = $this->input;

		return $attribs;
	}

	protected function processInput() {
		$this->processedInput = $this->input;
		if ( $this->tag->needsParsedInput() ) {
			$this->processedInput =
				$this->parser->recursiveTagParse( $this->processedInput, $this->frame );
		}

		$paramDefinition = $this->tag->getInputDefinition();
		if ( $paramDefinition === null ) {
			return;
		}
		$paramName = $paramDefinition->getName();
		$paramDefinition->setMessage(
			wfMessage( 'mwstake-components-generictaghandler-tag-input-desc' )->plain()
		);

		$options = new Options();
		$options->setName( 'input-text' );
		$processor = Processor::newFromOptions( $options );
		$processor->setParameters(
			[ $paramName => $this->processedInput ],
			[ $paramDefinition ]
		);

		$result = $processor->processParameters();
		$this->checkForProcessingErrors( $result );
		$processedParams = $result->getParameters();

		$this->processedInput = $processedParams[ $paramName ]->getValue();
	}

	protected function processArgs() {
		$this->processedArgs = $this->args;
		if ( $this->tag->needsParseArgs() ) {
			foreach ( $this->processedArgs as &$processedArg ) {
				$processedArg =
					$this->parser->recursiveTagParse( $processedArg, $this->frame );
			}
		}

		$paramDefinitions = $this->tag->getArgsDefinitions();
		if ( count( $paramDefinitions ) === 0 ) {
			return;
		}

		$rawArgs = $this->processedArgs;
		$this->processedArgs = [];

		foreach ( $paramDefinitions as $paramDefinition ) {
			$paramDefinition->setMessage(
				wfMessage(
					'mwstake-components-generictaghandler-tag-param-desc',
					$paramDefinition->getName()
				)->plain()
			);
			$options = new \ParamProcessor\Options();
			$options->setName( 'arg-' . $paramDefinition->getName() );
			$processor = \ParamProcessor\Processor::newFromOptions( $options );
			$localArgs = [];
			$names = array_merge(
				[ $paramDefinition->getName() ],
				$paramDefinition->getAliases()
			);

			foreach ( $names as $name ) {
				if ( isset( $rawArgs[$name] ) ) {
					$localArgs[$name] = $rawArgs[$name];
				}
			}
			$processor->setParameters( $localArgs, [ $paramDefinition ] );

			$result = $processor->processParameters();
			$this->checkForProcessingErrors( $result );

			foreach ( $result->getParameters() as $processedParam ) {
				$this->processedArgs[$processedParam->getName()]
					= $processedParam->getValue();
			}
		}
	}

	/**
	 *
	 * @return bool
	 */
	protected function hasErrors() {
		return count( $this->errors ) !== 0;
	}

	/**
	 *
	 * @return array
	 */
	protected function makeErrorOutput() {
		$out = [];
		$translator = new ProcessingErrorMessageTranslator();
		foreach ( $this->errors as $errorKey => $errorMessage ) {
			$translatedMessage = $translator->translate( $errorMessage );
			$label = $this->makeErrorLabel( $errorKey );
			$out[] = Html::element(
				'div',
				[ 'class' => 'mwstake-components-generictaghandler-error mwstake-components-generictaghandler-tag' ],
				$label . $translatedMessage
			);
		}
		return $out;
	}

	/**
	 *
	 * @param string $elementName
	 * @return bool
	 */
	protected function isValidContainerElementName( $elementName ) {
		return in_array( $elementName, $this->getValidElementNames() );
	}

	protected function addResourceLoaderModules() {
		$this->parser->getOutput()->addModules( $this->tag->getResourceLoaderModules() );

		$this->parser->getOutput()->addModuleStyles( $this->tag->getResourceLoaderModuleStyles() );
	}

	/**
	 *
	 * @param \ParamProcessor\ProcessingResult $result
	 */
	protected function checkForProcessingErrors( $result ) {
		foreach ( $result->getErrors() as $error ) {
			$this->errors[$error->getElement()] = $error->getMessage();
		}
	}

	/**
	 *
	 * @param string $errorKey
	 * @return string
	 */
	protected function makeErrorLabel( $errorKey ) {
		$keyParts = explode( '-', $errorKey, 2 );
		$argName = end( $keyParts );
		if ( $keyParts[0] === 'input' ) {
			return '';
		}

		return "$argName: ";
	}

}
