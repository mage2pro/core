<?php
/**
 * 2017-06-09
 * @used-by df_oqi_desc()
 * @used-by \Df\Payment\Charge::text()
 * @used-by \Dfe\IPay88\Charge::pCharge()
 * @used-by \Dfe\Qiwi\Charge::pBill()
 * @used-by \Dfe\TwoCheckout\LineItem::adjustText()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
 * @used-by \Dfe\YandexKassa\Result::__toString()
 * @param string $s
 * @param int|null $max [optional]
 * @return string
 */
function df_chop($s, $max = null) {return !$max || (mb_strlen($s = df_trim($s)) <= $max) ? $s :
	df_trim_right(mb_substr($s, 0, $max - 1)) . '…'
;}

/**
 * Обратите внимание, что иногда вместо данной функции надо применять @see trim().
 * Например, @see df_trim() не умеет отсекать нулевые байты,
 * которые могут образовываться на конце строки
 * в результате шифрации, передачи по сети прямо в двоичном формате, и затем обратной дешифрации
 * посредством @see Varien_Crypt_Mcrypt.
 * @see Df_Core_Model_RemoteControl_Coder::decode()
 * @see Df_Core_Model_RemoteControl_Coder::encode()
 * 2017-07-01 Добавил параметр $throw.
 * @param string|string[] $s
 * @param string $charlist [optional]
 * @param bool|mixed|\Closure $throw [optional]
 * @return string|string[]
 */
function df_trim($s, $charlist = null, $throw = false) {return df_try(function() use($s, $charlist, $throw) {
	/** @var string|string $result */
	if (is_array($s)) {
		$result = df_map('df_trim', $s, [$charlist, $throw]);
	}
	else {
		if (!is_null($charlist)) {
			/** @var string[] $addionalSymbolsToTrim */
			$addionalSymbolsToTrim = ["\n", "\r", ' '];
			foreach ($addionalSymbolsToTrim as $addionalSymbolToTrim) {
				/** @var string $addionalSymbolToTrim */
				if (!df_contains($charlist, $addionalSymbolToTrim)) {
					$charlist .= $addionalSymbolToTrim;
				}
			}
		}
		/**
		 * Обратите внимание, что класс Zend_Filter_StringTrim может работать некорректно
		 * для строк, заканчивающихся заглавной кириллической буквой «Р».
		 * http://framework.zend.com/issues/browse/ZF-11223
		 * Однако решение, которое предложено по ссылке выше
		 * (http://framework.zend.com/issues/browse/ZF-11223)
		 * может приводить к падению интерпретатора PHP
		 * для строк, начинающихся с заглавной кириллической буквы «Р».
		 * Такое у меня происходило в методе @see Df_Autotrading_Model_Request_Locations::parseLocation()
		 * Кто виноват: решение или исходный класс @see Zend_Filter_StringTrim — не знаю
		 * (скорее, решение).
		 * Поэтому мой класс @see \Df\Zf\Filter\StringTrim дополняет решение по ссылке выше
		 * программным кодом из Zend Framework 2.0.
		 */
		/** @var \Df\Zf\Filter\StringTrim $filter */
		$filter = new \Df\Zf\Filter\StringTrim($charlist);
		$result = $filter->filter($s);
		/**
		 * @see Zend_Filter_StringTrim::filter() теоретически может вернуть null,
		 * потому что этот метод зачастую перепоручает вычисление результата функции @uses preg_replace()
		 * @url http://php.net/manual/function.preg-replace.php
		 */
		$result = df_nts($result);
		// Как ни странно, Zend_Filter_StringTrim иногда выдаёт результат « ».
		if (' ' === $result) {
			$result = '';
		}
	}
	return $result;
}, false === $throw ? $s : $throw);}

/**
 * Пусть пока будет так. Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses ltrim()
 * @used-by df_trim_ds_left()
 * @used-by df_url_bp()
 * @used-by \Df\Config\Settings::phpNameToKey()
 * @used-by \Dfe\PostFinance\W\Event::cardNumber()
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_left($s, $charlist = null) {return ltrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * Пусть пока будет так. Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses rtrim()
 * @used-by df_chop()
 * @used-by df_trim_ds_right()
 * @used-by \Dfr\Core\Realtime\Dictionary::_continue()
 * @used-by \Dfr\Core\Realtime\Dictionary\ModulePart\Block::getTemplateEnd()
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_right($s, $charlist = null) {return rtrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * Отсекает у строки $haystack подстроку $needle,
 * если она встречается в начале или в конце строки $haystack
 * 2016-10-28
 * Добавил поддержку нескольких $needle.
 * @param string $haystack
 * @param string|string[] $needle
 * @return string
 */
function df_trim_text($haystack, $needle) {return
	df_trim_text_left(df_trim_text_right($haystack, $needle), $needle)
;}

/**
 * 2016-10-28
 * @used-by df_trim_text_left()
 * @used-by df_trim_text_right()
 * @param string $haystack
 * @param string[] $needle
 * @param callable $f
 * @return string
 */
function df_trim_text_a($haystack, array $needle, callable $f) {
	/** @var string $result */
	$result = $haystack;
	/** @var int $length */
	$length = mb_strlen($result);
	foreach ($needle as $needleItem) {
		/** @var string $needleItem */
		$result = call_user_func($f, $result, $needleItem);
		if ($length !== mb_strlen($result)) {
			break;
		}
	}
	return $result;
}

/**
 * Отсекает у строки $haystack заданное начало $needle.
 * 2016-10-28 Добавил поддержку нескольких $needle.
 * @used-by dfsm_code_short()
 * @param string $haystack
 * @param string|string[] $needle
 * @return string
 */
function df_trim_text_left($haystack, $needle) {
	/** @var string $result */
	if (is_array($needle)) {
		/** @var string $result */
		$result = df_trim_text_a($haystack, $needle, __FUNCTION__);
	}
	else {
		/** @var int $length */
		$length = mb_strlen($needle);
		/** @see df_starts_with() */
		$result =
			($needle === mb_substr($haystack, 0, $length))
			? mb_substr($haystack, $length)
			: $haystack
		;
	}
	return $result;
}

/**
 * Отсекает у строки $haystack заданное окончание $needle.
 * 2016-10-28
 * Добавил поддержку нескольких $needle.
 * @param string $haystack
 * @param string|string[] $needle
 * @return string
 */
function df_trim_text_right($haystack, $needle) {
	/** @var string $result */
	if (is_array($needle)) {
		/** @var string $result */
		$result = df_trim_text_a($haystack, $needle, __FUNCTION__);
	}
	else {
		/** @var int $length */
		$length = mb_strlen($needle);
		/** @see df_ends_with() */
		$result =
			(0 !== $length) && ($needle === mb_substr($haystack, -$length))
			? mb_substr($haystack, 0, -$length)
			: $haystack
		;
	}
	return $result;
}