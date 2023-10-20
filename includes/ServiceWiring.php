<?php

use MWStake\MediaWiki\Component\GenericTagHandler\TagFactory;

return [
	'MWStakeTagFactory' => static function (): TagFactory {
		return new TagFactory();
	}
];
