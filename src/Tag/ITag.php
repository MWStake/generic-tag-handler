<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler;

use ParamProcessor\ParamDefinition;
use Parser;
use PPFrame;

interface ITag {
	/**
	 * @return string[]
	 */
	public function getTagNames();

	/**
	 * @param mixed $processedInput
	 * @param array $processedArgs
	 * @param \Parser $parser
	 * @param \PPFrame $frame
	 *
	 * @return IHandler
	 */
	public function getHandler( $processedInput, array $processedArgs, Parser $parser,
		PPFrame $frame );

	/**
	 * Returning an empty string will result in no container to be created.
	 * This also means no automatic data attributes will be available.
	 *
	 * @return string
	 */
	public function getContainerElementName();

	/**
	 * @return bool
	 */
	public function needsDisabledParserCache();

	/**
	 * @return bool
	 */
	public function needsParsedInput();

	/**
	 * @return bool
	 */
	public function needsParseArgs();

	/**
	 * @return string[]
	 */
	public function getResourceLoaderModules();

	/**
	 * @return string[]
	 */
	public function getResourceLoaderModuleStyles();

	/**
	 * @return MarkerType
	 */
	public function getMarkerType();

	/**
	 * @return ?ParamDefinition
	 */
	public function getInputDefinition();

	/**
	 * @return ParamDefinition[]
	 */
	public function getArgsDefinitions();
}
