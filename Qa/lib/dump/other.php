<?php
/**
 * 1) Иногда я для разработки использую заплатку ядра для xDebug — отключаю @see set_error_handler() для режима разработчика.
 * 2) Так вот, xDebug при обработке фатальных сбоев (в том числе и `E_RECOVERABLE_ERROR`),
 * выводит на экран диагностическое сообщение, и после этого останавливает работу интерпретатора.
 * 3) Конечно, если у нас сбой типов
 * 		`E_COMPILE_ERROR`, `E_COMPILE_WARNING`, `E_CORE_ERROR`, `E_CORE_WARNING`, `E_ERROR`, `E_PARSE`,
 * то и @see set_error_handler() не поможет (не обрабатывает эти типы сбоев, согласно официальной документации PHP).
 * 4) Однако сбои типа `E_RECOVERABLE_ERROR` обработик сбоев Magento, установленный посредством @see set_error_handler(),
 * переводит в исключительние ситуации.
 * 5) xDebug же при `E_RECOVERABLE_ERROR` останавивает работу интерпретатора, что нехорошо.
 * 6) Поэтому для функций, которые могут привести к `E_RECOVERABLE_ERROR`, пишем обёртки,
 * которые вместо `E_RECOVERABLE_ERROR` возбуждают исключительную ситуацию.
 * 7) Одна из таких функций — df_string.
 * @used-by df_type()
 * @used-by \Df\Xml\G::importString()
 * @param mixed $v
 */
function df_string($v):string {
	df_assert(!is_array($v), 'The developer wrongly treats an array as a string.');
	/**
	 * 2016-09-04
	 * К сожалению, нельзя здесь для проверки публичности метода `__toString()` использовать @see is_callable(),
	 * потому что наличие @see \Magento\Framework\DataObject::__call() приводит к тому, что `is_callable` всегда возвращает `true`.
	 * @uses method_exists(), в отличие от `is_callable`, не гарантирует публичную доступность метода:
	 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
	 * потому что он имеет доступность `private` или `protected`.
	 * Пока эта проблема никак не решена.
	 */
	df_assert(!is_object($v) || method_exists($v, '__toString'),
		'The developer wrongly treats an object of the class `%s` as a string.', get_class($v)
	);
	return strval($v);
}

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