<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler\ParamDefinition;

use MWStake\MediaWiki\Component\GenericTagHandler\Validator\TitleValidator;
use ParamProcessor\ParamDefinition as Definition;

/** @inheritdoc */
class TitleListParam extends Definition {
	protected $delimiter = '|';
	protected $validator = null;

	protected function postConstruct() {
		$this->validator = new TitleValidator();
	}

	public function isList(): bool {
		return true;
	}
}
