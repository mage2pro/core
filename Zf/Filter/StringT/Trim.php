<?php
/**
 * Обратите внимание, что у нас 2 класса-фильтра для обрубки строк.
 * @see Df_Zf_Filter_String_Trim
 * @see \Df\Zf\Filter\StringTrim
 * Класс @see \Df\Zf\Filter\StringTrim — это всего лишь наследник-замена-заплатка
 * для стандартного класса Zend Framework @see Zend_Filter_StringTrim.
 * Класс @see \Df\Zf\Filter\StringT\Trim делает намного больше, чем класс @see \Df\Zf\Filter\StringTrim.
 * Класс @see \Df\Zf\Filter\StringT\Trim инкапсулирует вызов @see df_trim,
 * который, в частности, автоматически добавляет в число фильтруемых символов символы «\r» и «\n».
 */
namespace Df\Zf\Filter\StringT;
class Trim implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws \Zend_Filter_Exception
	 * @return string
	 */
	function filter($value) {
		/** @var string $result */
		try {
			$result = df_trim($value);
		}
		catch (\Exception $e) {
			df_error(new \Zend_Filter_Exception(df_ets($e)));
		}
		return $result;
	}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}