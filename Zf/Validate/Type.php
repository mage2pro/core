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
	protected function getMessageInternal() {
		return
			is_null($this->getValue())
			? $this->getDiagnosticMessageForNull()
			: $this->getDiagnosticMessageForNotNull()
		;
	}

	/** @return string */
	private function getDiagnosticMessageForNotNull() {
		return strtr(
			'Система не смогла распознать значение «{значение}» типа «{тип}» как {требуемый тип}.',
			array(
				'{значение}' => df_string_debug($this->getValue()),
				'{тип}' => gettype($this->getValue()),
				'{требуемый тип}' => $this->getExpectedTypeInAccusativeCase()
			)
		);
	}

	/** @return string */
	private function getDiagnosticMessageForNull() {
		return strtr(
			'Система вместо {требуемый тип} получила «NULL».',
			array('{требуемый тип}' => $this->getExpectedTypeInGenitiveCase())
		) ;
	}
}