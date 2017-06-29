<?php
namespace Df\Config\Backend;
use Df\Config\Backend;
/**
 * 2015-12-21
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * Используем этот класс для устранения дефекта
 * «The fields with type='checkbox' are not saved in the backend «Stores» → «Configuration» section»
 * https://mage2.pro/t/333
 */
class Checkbox extends Backend {
	/**
	 * 2015-12-21 Когда чекбокс установлен, то в массиве приходит пустая строка, а когда не установлен, то null.
	 * @override
	 * @see \Df\Config\Backend::dfSaveBefore()
	 * @used-by \Df\Config\Backend::save()
	 */
	final protected function dfSaveBefore() {
		if ('' === $this->getValue()) {
			$this->setValue(true);
		}
		parent::dfSaveBefore();
	}
}


