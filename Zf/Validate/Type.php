<?php
namespace Df\Zf\Validate;
abstract class Type extends \Df\Zf\Validate {
	/** @return string */
	abstract protected function getExpectedTypeInAccusativeCase();
	/** @return string */
	abstract protected function getExpectedTypeInGenitiveCase();

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageInternal() {return
		is_null($this->getValue()) ? $this->getDiagnosticMessageForNull() : $this->getDiagnosticMessageForNotNull()
	;}

	/** @return string */
	private function getDiagnosticMessageForNotNull() {return strtr(
		'Unable to recognize the value «{value}» of type «{type}» as {expected type}.', [
			'{value}' => df_string_debug($this->getValue()),
			'{type}' => gettype($this->getValue()),
			'{expected type}' => $this->getExpectedTypeInAccusativeCase()
		]
	);}

	/** @return string */
	private function getDiagnosticMessageForNull() {return
		"Got `NULL` instead of {$this->getExpectedTypeInGenitiveCase()}.";
	}
}