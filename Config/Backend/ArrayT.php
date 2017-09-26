<?php
namespace Df\Config\Backend;
use Df\Config\A;
use Df\Config\ArrayItem as I;
// 2016-07-30
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class ArrayT extends Serialized {
	/**
	 * По поводу удаления @see \Df\Config\A::FAKE:
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
	 * По поводу валидации:
	 * 2016-08-02
	 * Частную валидацию объектов проводим обязательно до проверки объектов на уникальность,
	 * потому что если данные объекты некорректны,
	 * то проверка на уникальность может дать некорректные результаты
	 * и даже привести к дополнителным сбоям.
	 *
	 * @override
	 * @see \Df\Config\Backend\Serialized::processI()
	 * @used-by \Df\Config\Backend\Serialized::processA()
	 * @param array(array(string => mixed)) $a
	 * @return array(array(string => mixed))
	 * @throws \Exception
	 */
	final protected function processI(array $a) {
		$a = array_values(dfa_unset($a, A::FAKE)); /** @var array(array(string => mixed)) $a */
		$e = iterator_to_array(A::i($this->entityC(), $a)); /** @var I[] $e */
		df_each($e, 'validate'); /** @uses \Df\Config\ArrayItem::validate() */
		if ($repeated = dfa_repeated(dfa_ids($e))) { /** @var int[]|string[] $repeated */
			df_error('The following values are not uniqie: %1.', df_csv_pretty($repeated));
		}
		$e = array_values(df_sort($e, 'sortWeight')); /** @uses \Df\Config\ArrayItem::sortWeight() */
		return df_each($e, 'getData');  /** @uses \Df\Config\ArrayItem::getData() */
	}
}