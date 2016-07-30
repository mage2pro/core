<?php
namespace Df\Config\Backend;
use Df\Config\A;
// 2016-07-30
class ArrayT extends Serialized {
	/**
	 * 2016-07-30
	 * Наша задача: удаление из массива всех целочисленных ключей, кроме @see \Df\Config\A::FAKE
	 * Целочисленные ключи — это идентификаторы строк.
	 * Нам, в принципе, без разницы их значения, лишь бы они были уникальными.
	 * Однако в клиетской части (JavaScript) нам удобно, чтобы они шли по порядку и без дыр.
	 * Более того, в алгоритме метода @see \Df\Framework\Form\Element\ArrayT::onFormInitialized()
	 * мы уже подразумеваем, что ключи именно такими и являются: идут с нуля и без пропусков.
	 * @override
	 * @see \Df\Config\Backend\Serialized::processA()
	 * @used-by \Df\Config\Backend\Serialized::valueSerialize()
	 * @used-by \Df\Config\Backend\Serialized::valueUnserialize()
	 * @param array(string => mixed) $value
	 * @return array(string|int => mixed)
	 */
	protected function processA(array $value) {
		return
			df_clean([A::FAKE => dfa($value, A::FAKE)]
		 	+ array_values(array_diff_key($value, array_flip([A::FAKE]))))
		;
	}
}