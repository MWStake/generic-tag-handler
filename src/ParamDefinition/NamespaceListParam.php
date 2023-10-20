<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler\ParamDefinition;

use MWStake\MediaWiki\Component\GenericTagHandler\Validator\NamespaceValidator;
use ParamProcessor\ParamDefinition as Definition;

/** @inheritdoc */
class NamespaceListParam extends Definition {
	protected $delimiter = '|';
	protected $validator = null;

	protected function postConstruct() {
		$this->validator = new NamespaceValidator();
	}

	/**
	 * @return bool
	 */
	public function isList(): bool {
		return true;
	}
}
