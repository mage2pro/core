<?php
use Magento\Framework\Model\AbstractModel as M;

/**
 * 2016-08-31
 * @param mixed[] $a
 * @return string
 */
function dfa_hash(array $a) {
	return array_reduce($a,
		/**
		 * @param string|null $result
		 * @param mixed $item
		 * @return string
		 */
		function($result, $item) {return (is_null($result) ? '' : $result . '::') .
			(is_object($item) ? dfo_hash($item) : (
				is_array($item) ? dfa_hash($item) : $item)
			)
		;}
	);
}

/**
 * 2016-09-04
 * @uses spl_object_hash() здесь используется не вполне корректно,
 * потому что эта функция может вернуть одно и то же значение для разных объектов,
 * если первый объект уже был уничтожен на момент повторного вызова spl_object_hash():
 * http://php.net/manual/en/function.spl-object-hash.php#76220
 * Но мы сознательно идём на этот небольшой риск :-)
 * Этот риск совсем мал, потому что для моделей мы не используем spl_object_hash(), а используем getId().
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