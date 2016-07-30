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
	 *
	 * Оказывается, и ключ @see \Df\Config\A::FAKE нам уже не нужен.
	 * Мы ведь его добавляли в скрипте https://github.com/mage2pro/core/tree/b1f6809/Framework/view/adminhtml/web/formElement/array/main.js#L131
	 * с такой целью:
	 * «2015-12-30
	 * Сервер так устроен, что если для конкретного поля формы
	 * не придут данные с сервер (а при отсутствии элементов они не придут),
	 * то сервер не обновляет значение в базе данных для этого поля.
	 * Это приводит к тому эффекту, что если удалить все элементы, то сервер не сохранит данные.
	 * Чтобы этого избежать, при отсутствии элементов передаём на сервер фейковый.»
	 *
	 * Поэтому теперь уже его можно удалить.
	 *
	 * @override
	 * @see \Df\Config\Backend\Serialized::processA()
	 * @used-by \Df\Config\Backend\Serialized::valueSerialize()
	 * @used-by \Df\Config\Backend\Serialized::valueUnserialize()
	 * @param array(string => mixed) $value
	 * @return array(string|int => mixed)
	 */
	protected function processA(array $value) {
		return array_values(array_diff_key($value, array_flip([A::FAKE])));
	}
}