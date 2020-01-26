<?php
use Df\Core\Helper\Text;
use Df\Core\Text\Regex;

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
 * @see df_bts_r()
 * @see df_bts_yn()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * function@used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t02()
 * @param boolean $v
 * @return string
 */
function df_bts($v) {return $v ? 'true' : 'false';}

/**
 * @see df_bts()
 * @see df_bts_yn()
 * @param boolean $v
 * @return string
 */
function df_bts_r($v) {return $v ? 'да' : 'нет';}

/**
 * 2017-11-08
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @see df_bts()
 * @see df_bts_r()
 * @param boolean $v
 * @return string
 */
function df_bts_yn($v) {return $v ? 'yes' : 'no';}

/**
 * 2015-04-17
 * Добавлена возможность указывать в качестве $needle массив.
 * Эта возможность используется в
 * @used-by df_assert_not_closure()
 * @used-by df_block_output()
 * @used-by df_request_ua()
 * @used-by df_url_path_contains()
 * @used-by ikf_ite()
 * @used-by mnr_recurring_is()
 * @used-by \Df\Framework\Logger\Handler\System::handle()
 * @used-by \Dfe\CurrencyFormat\Plugin\Catalog\Controller\Adminhtml\Product\Initialization\Helper\AttributeFilter::parse()
 * @used-by \RWCandy\Captcha\Assert::name()
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
 * @used-by df_fetch_one()
 * @used-by df_parent_name()
 * @used-by \Df\Core\O::cacheLoadProperty()
 * @used-by \Df\Xml\X::descend()
 * @used-by \Dfe\Stripe\Init\Action::need3DS()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
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
 * 2016-08-04
 * @used-by dfa_deep()
 * @used-by \Df\Payment\Block\Info::si()
 * @used-by \Df\Xml\Parser\Entity::descendWithCast()
 * @param mixed $v
 * @return bool
 */
function df_nes($v) {return is_null($v) || '' === $v;}

/**
 * @param mixed|null $v
 * @return mixed
 */
function df_nts($v) {return !is_null($v) ? $v : '';}

/**
 * @used-by \Df\Typography\Font::variantNumber()
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return int|null|bool
 */
function df_preg_int($pattern, $subject, $throwOnNotMatch = false) {return Regex::i(
	$pattern, $subject, $throwOnError = true, $throwOnNotMatch
)->matchInt();}

/**
 * 2015-03-23 Добавил поддержку нескольких пар круглых скобок (в этом случае функция возвращает массив).
 * @used-by df_preg_prefix()
 * @used-by df_xml_parse_header()
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return string|string[]|null|bool
 */
function df_preg_match($pattern, $subject, $throwOnNotMatch = false) {return Regex::i(
	$pattern, $subject, $throwOnError = true, $throwOnNotMatch
)->match();}

/**
 * 2018-11-11
 * @used-by \Dfe\TBCBank\T\CaseT\Validator::t01()
 * @param string $prefix
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return int|null|bool
 */
function df_preg_prefix($prefix, $subject, $throwOnNotMatch = false) {return df_preg_match(
	sprintf('#^%s([\S\s]*)#', preg_quote($prefix)), $subject, $throwOnNotMatch
);}

/**
 * @used-by df_has_russian_letters()
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnError [optional]
 * @return bool
 * @throws \Exception
 */
function df_preg_test($pattern, $subject, $throwOnError = true) {return Regex::i(
	$pattern, $subject, $throwOnError, $throwOnNotMatch = false
)->test();}

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
			df_error('Программист ошибочно пытается трактовать объект класса %s как строку.', get_class($value));
		}
	}
	elseif (is_array($value)) {
		df_error('Программист ошибочно пытается трактовать массив как строку.');
	}
	return strval($value);
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
 * 2016-07-05
 * @used-by \Df\Core\O::getId()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop_2()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_rename()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_rename()
 * @used-by \Df\Framework\Form\Element\Multiselect::getElementHtml()
 * @used-by \Df\Sso\Button::attributes()
 * @used-by \Dfe\Moip\P\Reg::p()
 * @used-by \Dfe\Moip\T\CaseT\Customer::pCustomer()
 * @used-by \Dfe\Moip\T\CaseT\Notification::create()
 * @used-by \Dfe\Moip\T\Order::pOrder()
 * @used-by \Dfe\Omise\T\Customer::tRetrieveNonExistent()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\Sift\Plugin\Customer\CustomerData\Customer::afterGetSectionData()
 * @used-by \Dfe\TBCBank\Charge::pCharge()
 * @used-by \Dfe\TBCBank\T\CaseT\Regular::transId()
 * @used-by \Dfe\Vantiv\T\CaseT\Charge::req()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t02()
 * @param int|null $length [optional]	Длина уникальной части, без учёта $prefix.
 * @param string $prefix [optional]
 * @return string
 */
function df_uid($length = null, $prefix = '') {
	// Важно использовать $more_entropy = true, потому что иначе на быстрых серверах
	// (я заметил такое поведение при использовании Zend Server Enterprise и PHP 5.4)
	// uniqid будет иногда возвращать одинаковые значения при некоторых двух последовательных вызовах.
	// 2016-07-05
	// При параметре $more_entropy = true значение будет содержать точку,
	// например: «4b340550242239.64159797».
	// Решил сегодня удалять эту точку из-за платёжной системы allPay,
	// которая требует, чтобы идентификаторы содержали только цифры и латинские буквы.
	$r = str_replace('.', '', uniqid($prefix, $more_entropy = true)); /** @var string $r */
	// Уникальным является именно окончание uniqid, а не начало.
	// Два последовательных вызова uniqid могу вернуть:
	// 5233061890334
	// 52330618915dd
	// Начало у этих значений — одинаковое, а вот окончание — различное.
	return $prefix . (!$length ? $r : substr($r, -$length));
}