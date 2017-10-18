<?php
use Df\Core\Helper\Text;
use Df\Core\Text\Regex;
use Magento\Framework\Phrase;

// 2015-12-31
// IntelliJ IDEA этого не показывает, но пробел здесь не обычный, а узкий.
// https://en.wikipedia.org/wiki/Thin_space
// Глобальные константы появились в PHP 5.3.
// http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html
const DF_THIN_SPACE = ' ';

/**
 * @see df_1251_to()
 * Если входной массив — ассоциативный и одномерный,
 * то и результат будет ассоциативным массивом: @see array_map().
 * @param string[] ...$args
 * @return string|string[]|array(string => string)
 */
function df_1251_from(...$args) {return df_call_a(function($text) {return
	// Насколько я понимаю, данному вызову равноценно:
	// iconv('windows-1251', 'utf-8', $s)
	mb_convert_encoding($text, 'UTF-8', 'Windows-1251')
;}, $args);}

/**
 * @see df_1251_from()
 * Если входной массив — ассоциативный и одномерный,
 * то и результат будет ассоциативным массивом: @uses array_map().
 * @param string[] ...$args
 * @return string|string[]|array(string => string)
 */
function df_1251_to(...$args) {return df_call_a(function($text) {return
	// Насколько я понимаю, данному вызову равноценно:
	// iconv('utf-8', 'windows-1251', $s)
	mb_convert_encoding($text, 'Windows-1251', 'UTF-8')
;}, $args);}

/**
 * 2017-04-22 «+79.6-2» => «7962»
 * http://stackoverflow.com/a/35619532
 * @param string $s
 * @return string
 */
function df_remove_non_digits($s) {return preg_replace('[\D]', '', $s);}

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

/**
 * 2016-10-17
 * @param string|string[] ...$elements
 * @return string
 */
function df_c(...$elements) {return implode(dfa_flatten($elements));}

/**
 * @param string $text
 * @return string
 */
function df_camelize($text) {return implode(df_ucfirst(df_explode_class(df_trim($text))));}

/**
 * @see df_ccc()
 * @param string $glue
 * @param string|string[] ...$elements
 * @return string
 */
function df_cc($glue, ...$elements) {return implode($glue, dfa_flatten($elements));}

/**
 * 2016-08-13
 * @used-by \Dfe\AllPay\Choice::title()
 * @used-by \Dfe\Square\API\Validator::short()
 * @param string[] ...$args
 * @return string
 */
function df_cc_br(...$args) {return df_ccc("<br>", dfa_flatten($args));}

/**
 * 2017-07-09
 * @used-by df_api_rr_failed()
 * @used-by \Df\API\Client::p()
 * @used-by \Df\Qa\Context::render()
 * @param array(string => string) $a
 * @param int|null $pad [optional]
 * @return string
 */
function df_cc_kv(array $a, $pad = null) {return df_cc_n(df_map_k(df_clean($a),
	function($k, $v) use($pad) {return
		(!$pad ? "$k: " : df_pad("$k:", $pad))
		.(is_array($v) || (is_object($v) && !method_exists($v, '__toString')) ? "\n" . df_json_encode($v) : $v)
	;}
));}

/**
 * @used-by df_cc_kv()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @param string[] ...$args
 * @return string
 */
function df_cc_n(...$args) {return df_ccc("\n", dfa_flatten($args));}

/**
 * 2015-12-01 Отныне всегда используем / вместо DIRECTORY_SEPARATOR.
 * @used-by df_js()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Df\Intl\Js::_toHtml()
 * @param string[] ...$args
 * @return string
 */
function df_cc_path(...$args) {return df_ccc('/', dfa_flatten($args));}

/**
 * 2016-05-31
 * @param string[] ...$args
 * @return string
 */
function df_cc_path_t(...$args) {return df_append(df_cc_path(dfa_flatten($args)), '/');}

/**
 * 2016-08-10
 * @used-by dfe_modules_info()
 * @used-by \Dfe\Square\Block\Info::prepare()
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @param string[] ...$args
 * @return string
 */
function df_cc_s(...$args) {return df_ccc(' ', dfa_flatten($args));}

/**
 * @see df_cc()
 * @param string $glue
 * @param string[] ...$elements
 * @return string
 */
function df_ccc($glue, ...$elements) {return implode($glue, df_clean(dfa_flatten($elements)));}

/**
 * 2017-06-09
 * @used-by df_oqi_desc()
 * @used-by \Df\Payment\Charge::text()
 * @used-by \Dfe\IPay88\Charge::pCharge()
 * @used-by \Dfe\Qiwi\Charge::pBill()
 * @used-by \Dfe\TwoCheckout\LineItem::adjustText()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
 * @used-by \Dfe\YandexKassa\Response::__toString()
 * @param string $s
 * @param int|null $max [optional]
 * @return string
 */
function df_chop($s, $max = null) {return !$max || (mb_strlen($s = df_trim($s)) <= $max) ? $s :
	df_trim_right(mb_substr($s, 0, $max - 1)) . '…'
;}

/**
 * 2015-04-17
 * Добавлена возможность указывать в качестве $needle массив.
 * Эта возможность используется в
 * @used-by Df_Admin_Config_Backend_Table::_afterLoad()
 * @param string $haystack
 * @param string|string[] ...$n
 * @return bool
 * Я так понимаю, здесь безопасно использовать @uses strpos вместо @see mb_strpos() даже для UTF-8.
 * http://stackoverflow.com/questions/13913411/mb-strpos-vs-strpos-whats-the-difference
 */
function df_contains($haystack, ...$n) {
	/** @var bool $result */
	// 2017-07-10 This branch is exclusively for optimization.
	if (1 === count($n) && !is_array($n0 = $n[0])) {
		$result = false !== strpos($haystack, $n0);
	}
	else {
		$result = false;
		$n = dfa_flatten($n);
		foreach ($n as $nItem) {
			/** @var string $nItem */
			if (false !== strpos($haystack, $nItem)) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}

/**
 * 2015-08-24
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function df_contains_ci($haystack, $needle) {return df_contains(
	mb_strtoupper($haystack), mb_strtoupper($needle)
);}

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
 * @used-by Dfe_Stripe/view/frontend/templates/multishipping.phtml
 * @param string[] ...$args
 * @return string|string[]
 */
function df_e(...$args) {return df_call_a(function($text) {return
	htmlspecialchars($text, ENT_QUOTES, 'UTF-8', $double_encode = false)
;}, $args);}

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
 * «YandexMarket» => array(«Yandex», «Market»)
 * «NewNASAModule» => array(«New», «NASA», «Module»)
 * http://stackoverflow.com/a/17122207
 *
 * 2016-08-24
 * http://php.net/manual/reference.pcre.pattern.modifiers.php
 * x (PCRE_EXTENDED)
 * 		If this modifier is set, whitespace data characters in the pattern are totally ignored
 * 		except when escaped or inside a character class,
 * 		and characters between an unescaped # outside a character class
 * 		and the next newline character, inclusive, are also ignored.
 *
 * 		This is equivalent to Perl's /x modifier,
 * 		and makes it possible to include commentary inside complicated patterns.
 *
 * 		Note, however, that this applies only to data characters.
 * 		Whitespace characters may never appear within special character sequences in a pattern,
 * 		for example within the sequence (?( which introduces a conditional subpattern.
 *
 * 2017-07-09
 * Note 1: ?<=
 * «Zero-width positive lookbehind assertion.
 * Continues match only if the subexpression matches at this position on the left.
 * For example, (?<=19)99 matches instances of 99 that follow 19.
 * This construct does not backtrack.»
 *
 * Note 2: ?=
 * «Zero-width positive lookahead assertion.
 * Continues match only if the subexpression matches at this position on the right.
 * For example, \w+(?=\d) matches a word followed by a digit, without matching the digit.
 * This construct does not backtrack.»
 *
 * I have extracted this explanation from Rad Software Regular Expression Designer
 * (it is a discontinued software, google for it),
 * and it get it from the .NET Framework 3.0 documentation:
 * https://msdn.microsoft.com/en-us/library/bs2twtah(v=vs.85).aspx
 *
 * Note 3.
 * Today I have changed «?=[A-Z0-9]» => «?=[A-Z0-9]», so now it handles the cases with digits, e.g.:
 * «Dynamics365» => [«Dynamics», «365»]
 *
 * @used-by df_explode_class_camel()
 * @used-by \Df\API\Client::p()
 * @param string[] ...$args
 * @return string[]|string[][]
 */
function df_explode_camel(...$args) {return df_call_a(function($name) {return preg_split(
	'#(?<=[a-z])(?=[A-Z0-9])#x', $name
);}, $args);}

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
 * @used-by \Dfe\AllPay\Charge::descriptionOnKiosk()
 * @used-by \Dfe\Moip\P\Charge::pInstructionLines()
 * @param string $s
 * @return string[]
 */
function df_explode_n($s) {return explode("\n", df_normalize($s));}

/**
 * 2016-09-03 Другой возможный алгоритм: df_explode_multiple(['/', DS], $path)
 * @used-by \Df\Config\Comment::groupPath()
 * @param string $path
 * @return string[]
 */
function df_explode_path($path) {return df_explode_xpath(df_path_n($path));}

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
 * @param mixed[] $args
 * @return string
 */
function df_format(...$args) {
	$args = df_args($args);
	/** @var string $result */
	$result = null;
	switch (count($args)) {
		case 0:
			$result = '';
			break;
		case 1:
			$result = $args[0];
			break;
		case 2:
			/** @var mixed $params */
			$params = $args[1];
			if (is_array($params)) {
				$result = strtr($args[0], $params);
			}
			break;
	}
	return !is_null($result) ? $result : df_sprintf($args);
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
 * 2016-01-14
 * @see df_ucfirst()
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see lcfirst()
 * @param string[] ...$args
 * @return string|string[]
 */
function df_lcfirst(...$args) {return df_call_a(function($s) {
	return mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1);
}, $args);}

/**
 * 2015-12-25
 * @param string $text
 * @return string
 */
function df_n_prepend($text) {return '' === $text ? '' : "\n" . $text;}

/**
 * 2016-08-04
 * @param mixed $v
 * @return bool
 */
function df_nes($v) {return is_null($v) || '' === $v;}

/**
 * @param string $text
 * @return string
 * http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 */
function df_normalize($text) {return strtr($text, ["\r\n" => "\n", "\r" => "\n"]);}

/**
 * @param mixed|null $v
 * @return mixed
 */
function df_nts($v) {return !is_null($v) ? $v : '';}

/**
 * Аналог @see str_pad() для Unicode.
 * http://stackoverflow.com/a/14773638
 * @used-by df_cc_kv()
 * @used-by \Df\Qa\Context::render()
 * @used-by \Df\Qa\State::param()
 * @used-by \Dfe\Moip\CardFormatter::label()
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
 *		Array
 *		(
 *			[pattern_id] => p2p
 *			[to] => 41001260130727
 *			[identifier_type] => account
 *			[amount] => 0.01
 *			[comment] => Оплата заказа №100000099 в магазине localhost.com.
 *			[message] =>
 *			[label] => localhost.com
 *		)
 * выводит:
 *	[pattern_id] => p2p
 *	[to] => 41001260130727
 *	[identifier_type] => account
 *	[amount] => 0.01
 *	[comment] => Оплата заказа №100000099 в магазине localhost.com.
 *	[message] =>
 *	[label] => localhost.com
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
 * @param string|string[]|Phrase|Phrase[] $text
 * @return string|string[]
 */
function df_quote_double($text) {return df_t()->quote($text, Text::QUOTE__DOUBLE);}

/**
 * @param string|string[]|Phrase|Phrase[] $text
 * @return string|string[]
 */
function df_quote_russian($text) {return df_t()->quote($text, Text::QUOTE__RUSSIAN);}

/**
 * @param string|string[]|Phrase|Phrase[] $text
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
 * Утверждают, что код ниже работает быстрее, чем return 0 === mb_strpos($haystack, $needle);
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_ends_with()
 * @used-by df_check_url_absolute()
 * @used-by df_handle_prefix()
 * @used-by dfe_modules_info()
 * @used-by dfe_packages()   
 * @used-by \Df\Payment\Observer\Multishipping::execute()
 * @used-by \Df\StripeClone\Facade\Charge::isCardId()
 * @param string $haystack
 * @param string|string[] $needle
 * @return bool
 */
function df_starts_with($haystack, $needle) {
	/** @var bool $result */
	if (!is_array($needle)) {
		$result = $needle === mb_substr($haystack, 0, mb_strlen($needle));
	}
	else {
		$result = false;
		foreach ($needle as $n) {
			/** @var string $n */
			if (df_starts_with($haystack, $n)) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}

/**
 * 2016-05-22
 * @param string[] ...$args
 * @return string|string[]
 */
function df_strtolower(...$args) {return df_call_a(function($s) {return mb_strtolower($s);}, $args);}

/**
 * 2016-05-19
 * @see df_lcfirst
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @param string[] ...$args
 * @return string|string[]
 */
function df_strtoupper(...$args) {return df_call_a(function($s) {
	return mb_strtoupper($s);
}, $args);}

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
	elseif (is_array($value)) {
		df_error('Программист ошибочно пытается трактовать массив как строку.');
	}
	return strval($value);
}

/**
 * 2015-03-03
 * Раньше алгоритм был таким:
 	 strtr($s, array_fill_keys($wordsToRemove, ''))
 * Он корректен, но новый алгоритм быстрее, потому что не требует вызова нестандартных функций.
 * http://php.net/str_replace
 * «If replace has fewer values than search,
 * then an empty string is used for the rest of replacement values.»
 * http://3v4l.org/9qvC4
 * @used-by df_phone_explode()
 * @used-by \Dfe\IPay88\Signer::adjust()
 * @param string $s
 * @param string[] $remove
 * @return string
 */
function df_string_clean($s, ...$remove) {return str_replace(dfa_flatten($remove), null, $s);}

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
	elseif (is_array($value)) {
		$result = sprintf('<массив из %d элементов>', count($value));
	}
	elseif (is_bool($value)) {
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
 * @used-by \Df\PaypalClone\W\Event::validate()
 * @param $s1
 * @param $s2
 * @return bool
 */
function df_strings_are_equal_ci($s1, $s2) {return 0 === strcmp(mb_strtolower($s1), mb_strtolower($s2));}

/** @return Text */
function df_t() {return Text::s();}

/**
 * @param string[] ...$args
 * @return string|string[]|array(string => string)
 */
function df_tab(...$args) {return df_call_a(function($text) {return "\t" . $text;}, $args);}

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
 * 2016-10-28
 * Добавил поддержку нескольких $needle.
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

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucfirst()
 * @see df_lcfirst
 * @param string[] ...$args
 * @return string|string[]
 */
function df_ucfirst(...$args) {return df_call_a(function($s) {
	return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
}, $args);}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucwords()
 * http://php.net/manual/function.mb-convert-case.php
 * http://php.net/manual/function.mb-convert-case.php#refsect1-function.mb-convert-case-parameters
 * @see df_ucfirst
 * @param string[] ...$args
 * @return string|string[]
 */
function df_ucwords(...$args) {return df_call_a(function($s) {
	return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
}, $args);}

/**
 * 2016-07-05
 * @param int|null $length [optional]	Длина уникальной части, без учёта $prefix.
 * @param string $prefix [optional]
 * @return string
 */
function df_uid($length = null, $prefix = '') {
	/** @var string $result */
	// Важно использовать $more_entropy = true, потому что иначе на быстрых серверах
	// (я заметил такое поведение при использовании Zend Server Enterprise и PHP 5.4)
	// uniqid будет иногда возвращать одинаковые значения при некоторых двух последовательных вызовах.
	// 2016-07-05
	// При параметре $more_entropy = true значение будет содержать точку,
	// например: «4b340550242239.64159797».
	// Решил сегодня удалять эту точку из-за платёжной системы allPay,
	// которая требует, чтобы идентификаторы содержали только цифры и латинские буквы.
	$result = str_replace('.', '', uniqid($prefix, $more_entropy = true));
	// Уникальным является именно окончание uniqid, а не начало.
	// Два последовательных вызова uniqid могу вернуть:
	// 5233061890334
	// 52330618915dd
	// Начало у этих значений — одинаковое, а вот окончание — различное.
	return $prefix . (!$length ? $result : substr($result, -$length));
}

/**
 * 2016-08-10
 * REFUND_ISSUED => RefundIssued
 * refund_issuED => RefundIssued
 * @param string[] ...$args
 * @return string|string[]
 */
function df_underscore_to_camel(...$args) {return df_call_a(function($s) {
	return implode(df_ucfirst(explode('_', mb_strtolower($s))));
}, $args);}

/**
 * 2016-03-09
 * Замещает переменные в тексте.
 * @used-by df_file_name()
 * @used-by \Df\GingerPaymentsBase\Block\Info::btInstructions()
 * @used-by \Df\Payment\Charge::text()
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Dfe\CheckoutCom\Response::messageC()
 * @used-by \Dfe\SalesSequence\Plugin\Model\Manager::affix()
 * 2016-08-07
 * Сегодня разработал аналогичные функции для JavaScript: df.string.template() и df.t()
 * @param string $s
 * @param array(string => string) $variables
 * @param string|callable|null $onUnknown
 * @return string
 */
function df_var($s, array $variables, $onUnknown = null) {return preg_replace_callback(
	'#\{([^\}]*)\}#ui', function($m) use($variables, $onUnknown) {return
		dfa($variables, dfa($m, 1, ''), $onUnknown)
	;}, $s
);}