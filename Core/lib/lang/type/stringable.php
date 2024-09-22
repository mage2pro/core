<?php
/**
 * 2024-09-22 PHP ≥ 8 has the @see Stringable interface: https://www.php.net/manual/en/class.stringable.php
 * @used-by df_assert_stringable()
 * @used-by df_kv()
 */
function df_is_stringable($v):bool {return !is_array($v) &&
	/**
	 * 2016-09-04
	 * К сожалению, нельзя здесь для проверки публичности метода `__toString()` использовать @see is_callable(),
	 * потому что наличие @see \Magento\Framework\DataObject::__call() приводит к тому, что `is_callable` всегда возвращает `true`.
	 * @uses method_exists(), в отличие от `is_callable`, не гарантирует публичную доступность метода:
	 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
	 * потому что он имеет доступность `private` или `protected`.
	 * Пока эта проблема никак не решена.
	 */
	(!is_object($v) || method_exists($v, '__toString'))
;}