<?php
use Df\Config\ArrayItem as AI;
use Magento\Framework\Model\AbstractModel as M;

/**
 * 2016-08-24
 * 2016-09-04
 * 1) Метод getId присутствует не только у потомков @see \Magento\Framework\Model\AbstractModel,
 * но и у классов сторонних библиотек, например:
 * https://github.com/CKOTech/checkout-php-library/blob/v1.2.4/com/checkout/ApiServices/Charges/ResponseModels/Charge.php?ts=4#L170-L173
 * По возможности, задействуем и сторонние реализации.
 * 2) К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
 * потому что наличие @see \Magento\Framework\DataObject::__call() приводит к тому, что `is_callable` всегда возвращает `true`.
 * @uses method_exists(), в отличие от `is_callable`, не гарантирует публичную доступность метода:
 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
 * потому что он имеет доступность `private` или `protected`.
 * Пока эта проблема никак не решена.
 * 2016-09-05
 * 1) Этот код прекрасно работает с объектами классов типа @see \Magento\Directory\Model\Currency
 * благодаря тому, что @uses \Magento\Framework\Model\AbstractModel::getId() не просто тупо считывает значение поля id,
 * а вызывает метод @see \Magento\Framework\Model\AbstractModel::getIdFieldName()
 * который, в свою очередь, узнаёт имя идентифицирующего поля из своего ресурса:
 * @see \Magento\Framework\Model\AbstractModel::_init()
 * @see \Magento\Directory\Model\ResourceModel\Currency::_construct()
 * 2) @see df_hash_o() использует тот же алгоритм, но не вызывает @see df_id() ради ускорения.
 * @used-by df_idn()
 * @used-by dfa_ids()
 * @see df_hash_o()
 * @param object|int|string $o
 * @return int|string|null
 */
function df_id($o, bool $allowNull = false) {/** @var int|string|null $r */
	$r = !is_object($o) ? $o : ($o instanceof M || method_exists($o, 'getId') ? $o->getId() : (
		$o instanceof AI ? $o->id() : null
	));
	df_assert($allowNull || $r);
	return $r;
}