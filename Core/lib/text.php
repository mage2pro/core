<?php
use Df\Core\Helper\Text;
use Df\Core\Model\Text\Regex;
/**
 * @see df_1251_to()
 * Если входной массив — ассоциативный и одномерный,
 * то и результат будет ассоциативным массивом: @see array_map().
 * @param ...
 * @return string|string[]|array(string => string)
 */
function df_1251_from() {return df_call_a(function($text) {
	// Насколько я понимаю, данному вызову равноценно:
	// iconv('windows-1251', 'utf-8', $s)
	return mb_convert_encoding($text, 'UTF-8', 'Windows-1251');
}, func_get_args());}

/**
 * @see df_1251_from()
 * Если входной массив — ассоциативный и одномерный,
 * то и результат будет ассоциативным массивом: @uses array_map().
 * @param ...
 * @return string|string[]|array(string => string)
 */
function df_1251_to() {return df_call_a(function($text) {
	// Насколько я понимаю, данному вызову равноценно:
	// iconv('utf-8', 'windows-1251', $s)
	return mb_convert_encoding($text, 'Windows-1251', 'UTF-8');
}, func_get_args());}

/**
 * 2016-03-08
 * Добавляет к строке $s окончание $tail,
 * если она в этой строке отсутствует.
 * @param string $s
 * @param string $tail
 * @return string
 */
function df_append($s, $tail) {return df_ends_with($s, $tail) ? $s : $s . $tail;}

/**
 * @param boolean $value
 * @return string
 */
function df_bts($value) {return $value ? 'true' : 'false';}

/**
 * @param boolean $value
 * @return string
 */
function df_bts_r($value) {return $value ? 'да' : 'нет';}

// 2015-12-31
// IntelliJ IDEA этого не показывает, но пробел здесь не обычный, а узкий.
// https://en.wikipedia.org/wiki/Thin_space
define('DF_THIN_SPACE', ' ');
/**
 * Эта функция отличается от @uses implode() тем,
 * что способна принимать переменное количество аргументов, например:
 * df_cc('aaa', 'bbb', 'ccc') вместо implode(array('aaa', 'bbb', 'ccc')).
 * То есть, эта функция даёт только сокращение синтаксиса.
 *
 * @uses implode() способна работать с одним аргументом,
 * и тогда параметр $glue считается равным пустой строке.
 * http://www.php.net//manual/function.implode.php
 *
 * @param ...
 * @return string
 */
function df_cc() {return implode(df_args(func_get_args()));}

/**
 * @param ...
 * @return string
 */
function df_cc_n() {return implode("\n", df_clean(df_args(func_get_args())));}

/**
 * 2015-12-01
 * Отныне всегда используем / вместо DIRECTORY_SEPARATOR.
 * @param ...
 * @return string
 */
function df_cc_path() {return implode('/', df_clean(df_args(func_get_args())));}

/**
 * 2016-05-31
 * @param ...
 * @return string
 */
function df_cc_path_t() {return df_append(df_cc_path(df_args(func_get_args())), '/');}

/**
 * @param ...
 * @return string
 */
function df_cc_url() {return implode('/', df_clean(df_args(func_get_args())));}

/**
 * 2016-05-31
 * @param ...
 * @return string
 */
function df_cc_url_t() {return df_append(df_cc_url(df_args(func_get_args())), '/');}

/**
 * @param ...
 * @return string
 */
function df_cc_xpath() {return implode('/', df_clean(df_args(func_get_args())));}

/**
 * 2016-05-31
 * @param ...
 * @return string
 */
function df_cc_xpath_t() {return df_append(df_cc_xpath(df_args(func_get_args())), '/');}

/**
 * 2015-04-17
 * Добавлена возможность указывать в качестве $needle массив.
 * Эта возможность используется в
 * @used-by Df_Admin_Config_Backend_Table::_afterLoad()
 * @param string $haystack
 * @param string|string[] $needle
 * @return bool
 * Я так понимаю, здесь безопасно использовать @uses strpos вместо @see mb_strpos() даже для UTF-8.
 * http://stackoverflow.com/questions/13913411/mb-strpos-vs-strpos-whats-the-difference
 */
function df_contains($haystack, $needle) {
	/** @var bool $result */
	if (!is_array($needle)) {
		$result = false !== strpos($haystack, $needle);
	}
	else {
		$result = false;
		foreach ($needle as $needleItem) {
			/** @var string $needleItem */
			if (false !== strpos($haystack, $needleItem)) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}

/**
 * 2015-02-07
 * Эта функция аналогична функции @see df_csv_pretty(),
 * но предназначена для тех обработчиков данных, которые не допускают пробелов между элементами.
 * Если обработчик данных допускает пробелы между элементами,
 * то для удобочитаемости данных используйте функцию @see df_csv_pretty().
 * @param ...
 * @return string
 */
function df_csv() {return implode(',', df_args(func_get_args()));}

/**
 * 2015-02-07
 * Второй параметр $delimiter используется, например методами:
 * @used-by Df_Localization_Model_Onetime_Dictionary_Rule_Conditions::getTargetTypes()
 * @used-by Df_Sales_Block_Admin_Grid_OrderItems::parseConcatenatedValues()
 * @param string|null $s
 * @param string $delimiter [optional]
 * @return string[]
 */
function df_csv_parse($s, $delimiter = ',') {return df_output()->parseCsv($s, $delimiter);}

/**
 * @param string|null $s
 * @return int[]
 */
function df_csv_parse_int($s) {return df_int(df_csv_parse($s));}

/**
 * 2015-02-07
 * Помимо данной функции имеется ещё аналогичная функция @see df_csv(),
 * которая предназначена для тех обработчиков данных, которые не допускают пробелов между элементами.
 * Если обработчик данных допускает пробелы между элементами,
 * то для удобочитаемости данных используйте функцию @see df_csv_pretty().
 * @param ...
 * @return string
 */
function df_csv_pretty() {return implode(', ', df_args(func_get_args()));}

/**
 * @param ...
 * @return string
 */
function df_csv_pretty_quote() {return df_csv_pretty(df_quote_russian(df_args(func_get_args())));}

/**
 * Обратите внимание, что мы намеренно не используем для @uses Df_Core_Dumper
 * объект-одиночку, потому что нам надо вести учёт выгруженных объектов,
 * чтобы не попасть в бесконечную рекурсию при циклических ссылках.
 * @param \Magento\Framework\DataObject|mixed[]|mixed $value
 * @return string
 */
function df_dump($value) {return \Df\Core\Dumper::i()->dump($value);}

/**
 * 2015-02-17
 * Не используем методы ядра
 * @see Mage_Core_Helper_Abstract::escapeHtml()
 * @see Mage_Core_Helper_Abstract::htmlEscape()
 * потому что они используют @uses htmlspecialchars() со вторым параметром @see ENT_COMPAT,
 * в результате чего одиночные кавычки не экранируются.
 * Ядро Magento не использует одиночные кавычки при формировании HTML
 * (в частности, в шаблонах *.phtml), поэтому, видимо, их устраивает режим ENT_COMPAT.
 * Российская сборка Magento использует при формировании HTML одиночные кавычки,
 * поэтому нам нужен режим ENT_QUOTES.
 * Это важно, например, в методе @used-by Df_Core_Model_Format_Html_Tag::getAttributeAsText()
 * @see df_ejs()
 * @param ...
 * @return string|string[]
 */
function df_e() {return df_call_a(function($text) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', $double_encode = false);
}, func_get_args());}

/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_starts_with()
 */
function df_ends_with($haystack, $needle) {
	/** @var int $length */
	$length = mb_strlen($needle);
	return (0 === $length) || ($needle === mb_substr($haystack, -$length));
}

/**
 * 'YandexMarket' => array('Yandex', 'Market')
 * 'NewNASAModule' => array('New', 'NASA', Module)
 * http://stackoverflow.com/a/17122207
 * @param ...
 * @return string[]|string[][]
 */

function df_explode_camel($name) {return df_call_a(function($name) {
	return preg_split('#(?<=[a-z])(?=[A-Z])#x', $name);
}, func_get_args());}

/**
 * 2016-03-25
 * «charge.dispute.funds_reinstated» => [charge, dispute, funds, reinstated]
 * @param string[] $delimiters
 * @param string $s
 * @return string[]
 */
function df_explode_multiple(array $delimiters, $s) {
	/** @var string $main */
	$main = array_shift($delimiters);
	/**
	 * 2016-03-25
	 * «If search is an array and replace is a string,
	 * then this replacement string is used for every value of search.»
	 * http://php.net/manual/function.str-replace.php
	 */
	return explode($main, str_replace($delimiters, $main, $s));
}

/**
 * @param string $s
 * @return string[]
 */
function df_explode_n($s) {return explode("\n", df_normalize($s));}

/**
 * @param string $url
 * @return string[]
 */
function df_explode_url($url) {return explode('/', $url);}

/**
 * @param string $xpath
 * @return string[]
 */
function df_explode_xpath($xpath) {return explode('/', $xpath);}

/**
 * @used-by df_flits()
 * @param float $value
 * @param int $precision [optional]
 * @return string
 */
function df_flts($value, $precision = 2) {return number_format($value, $precision, '.', '');}

/**
 * @param int|float $value
 * @param int $precision [optional]
 * @return string
 */
function df_flits($value, $precision = 2) {
	return is_int($value) ? (string)$value : df_flts($value, $precision);
}

/**
 * @used-by df_error()
 * @used-by Df_Core_Model_Logger::log()
 * @used-by Df_Core_Model_Logger::logRaw()
 * @used-by Df_Core_Model_SimpleXml_Generator_Part::log()
 * @used-by Df_Core_Model_SimpleXml_Generator_Part::notify()
 * @param mixed[] $arguments
 * @return string
 */
function df_format(array $arguments) {
	/** @var string $result */
	$result = null;
	/** @var int $count */
	$count = count($arguments);
	df_assert_gt0($count);
	switch ($count) {
		case 1:
			$result = $arguments[0];
			break;
		case 2:
			/** @var mixed $params */
			$params = $arguments[1];
			if (is_array($params)) {
				$result = strtr($arguments[0], $params);
			}
			break;
	}
	return !is_null($result) ? $result : df_sprintf($arguments);
}

/**
 * @param mixed|false $value
 * @return mixed|null
 */
function df_ftn($value) {return (false === $value) ? null : $value;}

/**
 * @param string $text
 * @return bool
 */
function df_has_russian_letters($text) {return df_preg_test('#[А-Яа-яЁё]#mui', $text);}

/**
 * @param string $value
 * @return string
 */
function df_json_prettify($value) {
	$value = df_t()->adjustCyrillicInJson($value);
	/** @var bool $h */
	static $h; if (is_null($h)) {$h = is_callable(['Zend_Json', 'prettyPrint']);};
	return $h ? Zend_Json::prettyPrint($value) : $value;
}

/**
 * 2016-01-14
 * @see df_ucfirst()
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see lcfirst()
 * @param ...
 * @return string|string[]
 */
function df_lcfirst() {return df_call_a(function($s) {
	return mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1);
}, func_get_args());}

/**
 * 2015-12-25
 * @param string $text
 * @return string
 */
function df_n_prepend($text) {return '' === $text ? '' : "\n" . $text;}

/**
 * @param string $text
 * @return string
 * http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 */
function df_normalize($text) {return strtr($text, ["\r\n" => "\n", "\r" => "\n"]);}

/**
 * Аналог @see str_pad() для Unicode.
 * http://stackoverflow.com/a/14773638
 * @used-by \Df\Qa\Context::render()
 * @used-by \Df\Qa\State::param()
 * @param string $phrase
 * @param int $length
 * @param string $pattern
 * @param int $position
 * @return string
 */
function df_pad($phrase, $length, $pattern = ' ', $position = STR_PAD_RIGHT) {
	/** @var string $encoding */
	$encoding = 'UTF-8';
	/** @var string $result */
	/** @var int $input_length */
	$input_length = mb_strlen($phrase, $encoding);
	/** @var int $pad_string_length */
	$pad_string_length = mb_strlen($pattern, $encoding);
	if ($length <= 0 || $length - $input_length <= 0) {
		$result = $phrase;
	}
	else {
		/** @var int $num_pad_chars */
		$num_pad_chars = $length - $input_length;
		/** @var int $left_pad */
		/** @var int $right_pad */
		switch ($position) {
			case STR_PAD_RIGHT:
				$left_pad = 0;
				$right_pad = $num_pad_chars;
				break;
			case STR_PAD_LEFT:
				$left_pad = $num_pad_chars;
				$right_pad = 0;
				break;
			case STR_PAD_BOTH:
				$left_pad = floor($num_pad_chars / 2);
				$right_pad = $num_pad_chars - $left_pad;
				break;
			default:
				df_error();
				break;
		}
		$result = '';
		for ($i = 0; $i < $left_pad; ++$i) {
			$result .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
		$result .= $phrase;
		for ($i = 0; $i < $right_pad; ++$i) {
			$result .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
	}
	return $result;
}

/**
 * 2015-11-29
 * Добавляет к строковому представлению целого числа нули слева.
 * 2015-12-01
 * Строковое представление может быть 16-ричным (код цвета), поэтому убрал @see df_int()
 * http://stackoverflow.com/a/1699980
 * @param int $length
 * @param int|string $number
 * @return string
 */
function df_pad0($length, $number) {return str_pad($number, $length, '0', STR_PAD_LEFT);}

/**
 * 2015-03-23
 * Добавил поддержку нескольких пар круглых скобок (в этом случае функция возвращает массив).
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return string|string[]|null|bool
 */
function df_preg_match($pattern, $subject, $throwOnNotMatch = true) {
	return Regex::i($pattern, $subject, $throwOnError = true, $throwOnNotMatch)->match();
}

/**
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return int|null|bool
 */
function df_preg_match_int($pattern, $subject, $throwOnNotMatch = true) {
	return Regex::i($pattern, $subject, $throwOnError = true, $throwOnNotMatch)->matchInt();
}

/**
 * @used-by df_has_russian_letters()
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnError [optional]
 * @return bool
 * @throws \Exception
 */
function df_preg_test($pattern, $subject, $throwOnError = true) {
	return Regex::i($pattern, $subject, $throwOnError, $throwOnNotMatch = false)->test();
}

/**
 * 2016-03-08
 * Добавляет к строке $s приставку $head,
 * если она в этой строке отсутствует.
 * @param string $s
 * @param string $head
 * @return string
 */
function df_prepend($s, $head) {return df_starts_with($s, $head) ? $s : $head . $s;}

/**
 * Эта функция имеет 2 отличия от @see print_r():
 * 1) она корректно обрабатывает объекты и циклические ссылки
 * 2) она для верхнего уровня не печатает обрамляющее «Array()» и табуляцию, т.е. вместо
		Array
		(
			[pattern_id] => p2p
			[to] => 41001260130727
			[identifier_type] => account
			[amount] => 0.01
			[comment] => Оплата заказа №100000099 в магазине localhost.com.
			[message] =>
			[label] => localhost.com
		)
 * выводит:
	[pattern_id] => p2p
	[to] => 41001260130727
	[identifier_type] => account
	[amount] => 0.01
	[comment] => Оплата заказа №100000099 в магазине localhost.com.
	[message] =>
	[label] => localhost.com
 *
 * @param array(string => string) $params
 * @return mixed
 */
function df_print_params(array $params) {return \Df\Core\Dumper::i()->dumpArrayElements($params);}

/**
 * @param string|mixed[] $pattern
 * @return string
 * @throws Exception
 */
function df_sprintf($pattern) {
	/** @var string $result */
	/** @var mixed[] $arguments */
	if (is_array($pattern)) {
		$arguments = $pattern;
		$pattern = df_first($arguments);
	}
	else {
		$arguments = func_get_args();
	}
	try {
		$result = df_sprintf_strict($arguments);
	}
	catch (Exception $e) {
		/** @var bool $inProcess */
		static $inProcess = false;
		if (!$inProcess) {
			$inProcess = true;
			//df_notify_me(df_ets($e));
			$inProcess = false;
		}
		$result = $pattern;
	}
	return $result;
}

/**
 * 2015-11-22
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_double($text) {return df_t()->quote($text, Text::QUOTE__DOUBLE);}

/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_russian($text) {return df_t()->quote($text, Text::QUOTE__RUSSIAN);}

/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_single($text) {return df_t()->quote($text, Text::QUOTE__SINGLE);}

/**
 * @param string|mixed[] $pattern
 * @return string
 * @throws \Exception
 */
function df_sprintf_strict($pattern) {
	/** @var mixed[] $arguments */
	if (is_array($pattern)) {
		$arguments = $pattern;
		$pattern = df_first($arguments);
	}
	else {
		$arguments = func_get_args();
	}
	/** @var string $result */
	if (1 === count($arguments)) {
		$result = $pattern;
	}
	else {
		try {
			$result = vsprintf($pattern, df_tail($arguments));
		}
		catch (Exception $e) {
			/** @var bool $inProcess */
			static $inProcess = false;
			if (!$inProcess) {
				$inProcess = true;
				df_error(
					'При выполнении sprintf произошёл сбой «{message}».'
					. "\nШаблон: {$pattern}."
					. "\nПараметры:\n{params}."
					,[
						'{message}' => df_ets($e)
						,'{params}' => print_r(df_tail($arguments), true)
					]
				);
				$inProcess = false;
			}
		}
	}
	return $result;
}

/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_ends_with()
 */
function df_starts_with($haystack, $needle) {
	/**
	 * Утверждают, что код ниже работает быстрее, чем
	 * return 0 === mb_strpos($haystack, $needle);
	 * http://stackoverflow.com/a/10473026
	 */
	/** @var int $length */
	$length = mb_strlen($needle);
	return ($needle === mb_substr($haystack, 0, $length));
}

/**
 * 2016-05-22
 * @param ...
 * @return string|string[]
 */
function df_strtolower() {return df_call_a(function($s) {
	return mb_strtolower($s);
}, func_get_args());}

/**
 * 2016-05-19
 * @see df_lcfirst
 * @param ...
 * @return string|string[]
 */
function df_strtoupper() {return df_call_a(function($s) {
	return mb_strtoupper($s);
}, func_get_args());}

/**
 * Иногда я для разработки использую заплатку ядра для xDebug —
 * отключаю set_error_handler для режима разработчика.
 *
 * Так вот, xDebug при обработке фатальных сбоев (в том числе и E_RECOVERABLE_ERROR),
 * выводит на экран диагностическое сообщение, и после этого останавливает работу интерпретатора.
 *
 * Конечно, если у нас сбой типов E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING,
 * E_COMPILE_ERROR, E_COMPILE_WARNING, то и set_error_handler не поможет
 * (не обрабатывает эти типы сбоев, согласно официальной документации PHP).
 *
 * Однако сбои типа E_RECOVERABLE_ERROR обработик сбоев Magento,
 * установленный посредством set_error_handler, переводит в исключительние ситуации.
 *
 * xDebug же при E_RECOVERABLE_ERROR останавивает работу интерпретатора, что нехорошо.
 *
 * Поэтому для функций, которые могут привести к E_RECOVERABLE_ERROR,
 * пишем обёртки, которые вместо E_RECOVERABLE_ERROR возбуждают исключительную ситуацию.
 * Одна из таких функций — df_string.
 *
 * @param mixed $value
 * @return string
 */
function df_string($value) {
	if (is_object($value)) {
		/**
		 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
		 * потому что наличие @see \Magento\Framework\DataObject::__call()
		 * приводит к тому, что @see is_callable всегда возвращает true.
		 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
		 * не гарантирует публичную доступность метода:
		 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
		 * потому что он имеет доступность private или protected.
		 * Пока эта проблема никак не решена.
		 */
		if (!method_exists($value, '__toString')) {
			df_error(
				'Программист ошибочно пытается трактовать объект класса %s как строку.'
				,get_class($value)
			);
		}
	}
	else if (is_array($value)) {
		df_error('Программист ошибочно пытается трактовать массив как строку.');
	}
	return strval($value);
}

/**
 * В настоящее время эта фукция не успользуется и осталасть только ради информации.
 * 2015-03-03
 * Раньше алгоритм был таким:
 	 strtr($s, array_fill_keys($wordsToRemove, ''))
 * Он корректен, но новый алгоритм быстрее, потому что не требует вызова нестандартных функций.
 * http://php.net/str_replace
 * «If replace has fewer values than search,
 * then an empty string is used for the rest of replacement values.»
 * http://3v4l.org/9qvC4
 * @param string $s
 * @param string|string[] $wordsToRemove
 * @return string
 */
function df_string_clean($s, $wordsToRemove) {
	if (!is_array($wordsToRemove)) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$wordsToRemove = df_tail($arguments);
	}
	return str_replace($wordsToRemove, null, $s);
}

/**
 * @param mixed $value
 * @return string
 */
function df_string_debug($value) {
	/** @var string $result */
	$result = '';
	if (is_object($value)) {
		/**
		 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
		 * потому что наличие @see \Magento\Framework\DataObject::__call()
		 * приводит к тому, что @see is_callable всегда возвращает true.
		 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
		 * не гарантирует публичную доступность метода:
		 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
		 * потому что он имеет доступность private или protected.
		 * Пока эта проблема никак не решена.
		 */
		if (!method_exists($value, '__toString')) {
			$result = get_class($value);
		}
	}
	else if (is_array($value)) {
		$result = sprintf('<массив из %d элементов>', count($value));
	}
	else if (is_bool($value)) {
		$result = $value ? 'логическое <да>' : 'логическое <нет>';
	}
	else {
		$result = strval($value);
	}
	return $result;
}

/**
 * @param string $s
 * @return array
 * http://us3.php.net/manual/en/function.str-split.php#107658
 */
function df_string_split($s) {return preg_split("//u", $s, -1, PREG_SPLIT_NO_EMPTY);}

/**
 * @param $s1
 * @param $s2
 * @return bool
 */
function df_strings_are_equal_ci($s1, $s2) {
	return 0 === strcmp(mb_strtolower($s1), mb_strtolower($s2));
}

/** @return Text */
function df_t() {return Text::s();}

/**
 * @param ...
 * @return string|string[]|array(string => string)
 */
function df_tab() {return df_call_a(function($text) {return "\t" . $text;}, func_get_args());}

/**
 * @param string $text
 * @return string
 */
function df_tab_multiline($text) {return df_cc_n(df_tab(df_explode_n($text)));}

/**
 * Обратите внимание, что иногда вместо данной функции надо применять @see trim().
 * Например, @see df_trim() не умеет отсекать нулевые байты,
 * которые могут образовываться на конце строки
 * в результате шифрации, передачи по сети прямо в двоичном формате, и затем обратной дешифрации
 * посредством @see Varien_Crypt_Mcrypt.
 * @see Df_Core_Model_RemoteControl_Coder::decode()
 * @see Df_Core_Model_RemoteControl_Coder::encode()
 * @param string|string[] $s
 * @param string $charlist [optional]
 * @return string|string[]
 */
function df_trim($s, $charlist = null) {return df_t()->trim($s, $charlist);}

/**
 * Отсекает у строки $haystack подстроку $needle,
 * если она встречается в начале или в конце строки $haystack
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function df_trim_text($haystack, $needle) {
	return df_trim_text_left(df_trim_text_right($haystack, $needle), $needle);
}

/**
 * Отсекает у строки $haystack заданное начало $needle
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function df_trim_text_left($haystack, $needle) {
	/** @var int $length */
	$length = mb_strlen($needle);
	/** @see df_starts_with() */
	return
		($needle === mb_substr($haystack, 0, $length))
		? mb_substr($haystack, $length)
		: $haystack
	;
}

/**
 * Отсекает у строки $haystack заданное окончание $needle
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function df_trim_text_right($haystack, $needle) {
	/** @var int $length */
	$length = mb_strlen($needle);
	/** @see df_ends_with() */
	return
		(0 !== $length) && ($needle === mb_substr($haystack, -$length))
		? mb_substr($haystack, 0, -$length)
		: $haystack
	;
}

/**
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_left($s, $charlist = null) {
	// Пусть пока будет так.
	// Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
	return ltrim($s, $charlist);
}

/**
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_right($s, $charlist = null) {
	// Пусть пока будет так.
	// Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
	return rtrim($s, $charlist);
}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucfirst()
 * @see df_lcfirst
 * @param ...
 * @return string|string[]
 */
function df_ucfirst() {return df_call_a(function($s) {
	return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
}, func_get_args());}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucwords()
 * http://php.net/manual/function.mb-convert-case.php
 * http://php.net/manual/function.mb-convert-case.php#refsect1-function.mb-convert-case-parameters
 * @see df_ucfirst
 * @param ...
 * @return string|string[]
 */
function df_ucwords() {return df_call_a(function($s) {
	return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
}, func_get_args());}

/**
 * @param int|null $length [optional]
 * @param string $prefix [optional]
 * @return string
 */
function df_uid($length = null, $prefix = '') {
	/** @var string $result */
	/**
	 * Важно использовать $more_entropy = true,
	 * потому что иначе на быстрых серверах
	 * (я заметил такое поведение при использовании Zend Server Enterprise и PHP 5.4)
	 * uniqid будет иногда возвращать одинаковые значения
	 * при некоторых двух последовательных вызовах.
	 */
	$result = uniqid($prefix, $more_entropy = true);
	if (!is_null($length)) {
		/**
		 * Обратите внимание, что уникальным является именно окончание uniqid, а не начало.
		 * Два последовательных вызова uniqid могу вернуть:
		 * 5233061890334
		 * 52330618915dd
		 * Начало у этих значений — одинаковое, а вот окончание — различное.
		 */
		$result = substr($result, -$length);
	}
	return $prefix . $result;
}

/**
 * 2016-03-09
 * Замещает переменные в тексте.
 * @used-by \Dfe\SalesSequence\Plugin\Model\Manager::affix()
 * @param string $s
 * @param array(string => string) $variables
 * @return string
 */
function df_var($s, array $variables) {
	return preg_replace_callback('#\{([^\}]*)\}#ui', function($m) use ($variables) {
		/** @var string $original */
		$original = dfa($m, 1, '');
		/** @var string $result */
		$result = strtr($original, $variables);
		return $result !== $original ? $result : date($result);
	}, $s);
}