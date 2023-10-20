<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler;

class ParamType {
	/* ParamProcessor library */
	public const BOOLEAN = 'boolean';
	public const FLOAT = 'float';
	public const INTEGER = 'integer';
	public const STRING = 'string';
	public const COORDINATE = 'coordinate';
	public const DIMENSION = 'dimension';

	/* MWStake Component */
	public const TITLE_LIST = 'titlelist';
	public const NAMESPACE_LIST = 'namespacelist';
	public const CATEGORY_LIST = 'categorylist';
}
