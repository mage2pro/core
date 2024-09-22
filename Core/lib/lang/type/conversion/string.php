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
 * @see df_dump()
 * @see df_is_stringable()
 * @used-by df_type()
 * @used-by \Df\Framework\W\Result\Json::prepare()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Df\Xml\G::importString()
 * @param mixed $v
 */
function df_string($v):string {return df_dump(strval(df_assert_stringable($v)));}