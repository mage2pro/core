<?php
use Df\Config\ArrayItem as AI;
use Magento\Framework\Model\AbstractModel as M;

/**
 * 2016-08-31
 * 2016-10-26
 * @see \Closure является объектом, и к ней можно применять @see spl_object_hash():
 * https://3v4l.org/Ok2k8
 * 2016-10-29 Раньше алгоритм работал через @see array_reduce(), но он не учитывал ключи массива.
 * @used-by df_cache_get_simple()
 * @used-by df_hash_a()
 * @used-by dfc()
 * @used-by dfcf()
 * @used-by \Df\API\Client::p()
 * @used-by \Df\Payment\Block\Info::getCacheKeyInfo()
 */
function df_hash_a(array $a):string {
	$resultA = []; /** @var string[] $resultA */
	foreach ($a as $k => $v) {/** @var int|string $k */ /** @var mixed $v */
		$resultA[]= "$k=>" . (is_object($v) ? df_hash_o($v) : (is_array($v) ? df_hash_a($v) : $v));
	}
	return implode('::', $resultA);
}

/**
 * 2016-09-04
 * @uses spl_object_hash() здесь используется не вполне корректно,
 * потому что эта функция может вернуть одно и то же значение для разных объектов,
 * если первый объект уже был уничтожен на момент повторного вызова spl_object_hash():
 * https://php.net/manual/function.spl-object-hash.php#76220
 * Но мы сознательно идём на этот небольшой риск :-)
 * Этот риск совсем мал, потому что для моделей мы не используем spl_object_hash(), а используем getId().
 * 2016-10-26 @see \Closure является объектом, и к ней можно применять @see spl_object_hash(): https://3v4l.org/Ok2k8
 * 2018-08-11 It would be better to use a more robust hashing solution for closures: https://stackoverflow.com/a/14620643
 * 2022-11-17
 * `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * @used-by df_hash_a()
 * @param object $o
 */
function df_hash_o($o):string {
	/** 2016-09-05 Для ускорения заменил вызов df_id($o, true) на инлайновыый код. @see df_id() */
	/** @var string $r */
	$r = $o instanceof M || method_exists($o, 'getId') ? $o->getId() : ($o instanceof AI ? $o->id() : null);
	return $r ? get_class($o) . "::$r" : spl_object_hash($o);
}