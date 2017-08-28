<?php
use Magento\Framework\Model\AbstractModel as M;

/**
 * 2016-08-31
 * 2016-10-26
 * @see \Closure является объектом, и к ней можно применять @see spl_object_hash():
 * https://3v4l.org/Ok2k8
 * 2016-10-29
 * Раньше алгоритм работал через @see array_reduce(), но он не учитывал ключи массива.
 * @param mixed[] $a
 * @used-by df_sentry()
 * @return string
 */
function dfa_hash(array $a) {
	$resultA = []; /** @var string[] $resultA */
	foreach ($a as $k => $v) {
		/** @var int|string $k */ /** @var mixed $v */
		$resultA[]= "$k=>" . (is_object($v) ? dfo_hash($v) : (is_array($v) ? dfa_hash($v) : $v));
	}
	return implode('::', $resultA);
}

/**
 * 2016-09-04
 * @uses spl_object_hash() здесь используется не вполне корректно,
 * потому что эта функция может вернуть одно и то же значение для разных объектов,
 * если первый объект уже был уничтожен на момент повторного вызова spl_object_hash():
 * http://php.net/manual/en/function.spl-object-hash.php#76220
 * Но мы сознательно идём на этот небольшой риск :-)
 * Этот риск совсем мал, потому что для моделей мы не используем spl_object_hash(), а используем getId().
 * 2016-10-26
 * @see \Closure является объектом, и к ней можно применять @see spl_object_hash():
 * https://3v4l.org/Ok2k8
 * @param object $o
 * @return string
 */
function dfo_hash($o) {
	/**
	 * 2016-09-05
	 * Для ускорения заменил вызов df_id($o, true) на инлайновыый код.
	 * @see df_id()
	 */
	/** @var string $result */
	$result = $o instanceof M || method_exists($o, 'getId') ? $o->getId() : null;
	return $result ? get_class($o) . '::' . $result : spl_object_hash($o);
}