<?php

namespace MWStake\MediaWiki\Component\GenericTagHandler\Validator;

use ValueValidators\TitleValidator as TitleValidatorBase;

class TitleValidator extends TitleValidatorBase {

	/**
	 *
	 * @var array
	 */
	protected $aNamespaceBlacklist = [];

	/**
	 *
	 * @var bool
	 */
	protected $isAllowedEmpty = false;

	/**
	 *
	 * @param array $aNamespaceBlacklist
	 */
	public function setNamespaceBlacklist( $aNamespaceBlacklist ) {
		$this->aNamespaceBlacklist = $aNamespaceBlacklist;
	}

	/**
	 *
	 * @param bool $isAllowedEmpty
	 */
	public function setIsAllowedEmpty( $isAllowedEmpty = true ) {
		$this->isAllowedEmpty = $isAllowedEmpty;
	}

	/**
	 *
	 * @param \Title $oTitle
	 */
	public function doValidation( $oTitle ) {
		if ( !$oTitle ) {
			if ( $this->isAllowedEmpty ) {
				return;
			}
			$this->addErrorMessage(
				wfMessage( 'mwstake-components-generictaghandler-validator-invalid-string' )->plain()
			);
			return;
		}
		if ( $this->hasToExist && !$oTitle->exists() ) {
			$this->addErrorMessage(
				wfMessage(
					'mwstake-components-generictaghandler-validator-error-title-does-not-exist',
					$oTitle->getPrefixedText()
				)->plain()
			);
		}

		if ( in_array( $oTitle->getNamespace(), $this->aNamespaceBlacklist ) ) {
			$this->addErrorMessage(
				wfMessage(
					'mwstake-components-generictaghandler-validator-error-title-namespace-on-blacklist',
					$oTitle->getPrefixedText()
				)->plain()
			);
		}
	}

	/**
	 *
	 * @param array $options
	 */
	public function setOptions( array $options ) {
		parent::setOptions( $options );

		if ( isset( $options['namespaceblacklist'] ) ) {
			$this->setNamespaceBlacklist( $options['namespaceblacklist'] );
		}
		if ( isset( $options['isallowedempty'] ) ) {
			$this->setIsAllowedEmpty( $options['isallowedempty'] ? true : false );
		}
	}
}
