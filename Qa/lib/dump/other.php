<?php
/**
 * @used-by \Df\Zf\Validate::message()
 * @param mixed $v
 */
function df_string_debug($v):string {
	$r = ''; /** @var string $r */
	if (is_object($v)) {
		/**
		 * 2016-09-04
		 * К сожалению, нельзя здесь для проверки публичности метода `__toString()` использовать @see is_callable(),
		 * потому что наличие @see \Magento\Framework\DataObject::__call() приводит к тому,
		 * что `is_callable` всегда возвращает `true`.
		 * @uses method_exists(), в отличие от `is_callable`, не гарантирует публичную доступность метода:
		 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
		 * потому что он имеет доступность `private` или `protected`.
		 * Пока эта проблема никак не решена.
		 */
		if (!method_exists($v, '__toString')) {
			$r = get_class($v);
		}
	}
	elseif (is_array($v)) {
		$r = sprintf('<an array of %d elements>', count($v));
	}
	elseif (is_bool($v)) {
		$r = $v ? 'logical <yes>' : 'logical <no>';
	}
	else {
		$r = strval($v);
	}
	return $r;
}