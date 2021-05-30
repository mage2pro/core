<?php
namespace Df\Zf\Validate;
/**
 * @see \Df\Zf\Validate\ArrayT
 * @see \Df\Zf\Validate\Boolean
 * @see \Df\Zf\Validate\FloatT
 * @see \Df\Zf\Validate\IntT
 * @see \Df\Zf\Validate\StringT
 * @see \Df\Zf\Validate\Uri
 */
abstract class Type extends \Df\Zf\Validate {
	/**
	 * @see \Df\Zf\Validate\IntT::getExpectedTypeInAccusativeCase()
	 * @used-by getDiagnosticMessageForNotNull()
	 * @return string
	 */
	abstract protected function getExpectedTypeInAccusativeCase();

	/**
	 * @see \Df\Zf\Validate\IntT::getExpectedTypeInGenitiveCase()
	 * @used-by getDiagnosticMessageForNull()
	 * @return string
	 */
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
	private function getDiagnosticMessageForNull() {return "Got `NULL` instead of {$this->getExpectedTypeInGenitiveCase()}.";}
}