<?php
use Df\Core\Helper\Text as T;
/**
 * 2015-12-31
 * 1) IntelliJ IDEA этого не показывает, но пробел здесь не обычный, а узкий: https://en.wikipedia.org/wiki/Thin_space
 * 2) Глобальные константы появились в PHP 5.3:
 * http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html
 * @used-by \Dfe\CurrencyFormat\O::options()
 * @used-by \Dfe\CurrencyFormat\O::thousandsSeparator()
 */
const DF_THIN_SPACE = ' ';

/**
 * @see df_bts_r()
 * @see df_bts_yn()
 * @used-by \Df\Qa\Dumper::dump()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t02()
 * @param bool $v
 * @return string
 */
function df_bts($v) {return $v ? 'true' : 'false';}

/**
 * @see df_bts()
 * @see df_bts_yn()
 * @param bool $v
 * @return string
 */
function df_bts_r($v) {return $v ? 'да' : 'нет';}

/**
 * 2017-11-08
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @see df_bts()
 * @see df_bts_r()
 * @param bool $v
 * @return string
 */
function df_bts_yn($v) {return $v ? 'yes' : 'no';}

/**
 * 2015-04-17 Добавлена возможность указывать в качестве $needle массив.
 * 2022-10-14 @see str_contains() has been added to PHP 8: https://www.php.net/manual/function.str-contains.php
 * @used-by df_block_output()
 * @used-by df_is_bin_magento()
 * @used-by df_request_ua()
 * @used-by df_rp_has()
 * @used-by ikf_ite()
 * @used-by mnr_recurring_is()
 * @used-by \Alignet\Paymecheckout\Plugin\Magento\Framework\Session\SidResolver::aroundGetSid() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \CanadaSatellite\Bambora\Facade::api() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Core\Plugin\Mageplaza\Blog\Helper\Data::afterGetBlogUrl() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/43)
 * @used-by \Df\Cron\Model\LoggerHandler::p()
 * @used-by \Df\Sentry\Trace::get_frame_context()
 * @used-by \Df\Xml\X::k()
 * @used-by \Dfe\CurrencyFormat\Plugin\Catalog\Controller\Adminhtml\Product\Initialization\Helper\AttributeFilter::parse()
 * @used-by \DxMoto\Core\Observer\CanLog::execute()
 * @used-by \RWCandy\Captcha\Assert::name()
 * @used-by \TFC\Core\Observer\CanLog::execute()
 * @param string $haystack
 * @param string|string[] ...$n
 * @return bool
 * Я так понимаю, здесь безопасно использовать @uses strpos вместо @see mb_strpos() даже для UTF-8.
 * http://stackoverflow.com/questions/13913411/mb-strpos-vs-strpos-whats-the-difference
 */
function df_contains($haystack, ...$n) {/** @var bool $r */
	# 2017-07-10 This branch is exclusively for optimization.
	if (1 === count($n) && !is_array($n0 = $n[0])) {
		$r = false !== strpos($haystack, $n0);
	}
	else {
		$r = false;
		$n = dfa_flatten($n);
		foreach ($n as $nItem) {/** @var string $nItem */
			if (false !== strpos($haystack, $nItem)) {
				$r = true;
				break;
			}
		}
	}
	return $r;
}

/**
 * 2015-08-24
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function df_contains_ci($haystack, $needle) {return df_contains(mb_strtoupper($haystack), mb_strtoupper($needle));}

/**
 * @param string $text
 * @return bool
 */
function df_has_russian_letters($text) {return df_preg_test('#[А-Яа-яЁё]#mui', $text);}

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
 * @used-by df_type()
 * @param mixed $v
 * @return string
 */
function df_string($v) {
	if (is_object($v)) {
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
		if (!method_exists($v, '__toString')) {
			df_error('The developer wrongly treats an object of the class %s as a string.', get_class($v));
		}
	}
	elseif (is_array($v)) {
		df_error('The developer wrongly treats an array as a string.');
	}
	return strval($v);
}

/**
 * @used-by \Df\Zf\Validate\Type::_message()
 * @param mixed $v
 * @return string
 */
function df_string_debug($v) {
	$r = ''; /** @var string $r */
	if (is_object($v)) {
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

/**
 * @used-by df_extend()
 * @used-by df_quote_double()
 * @used-by df_quote_russian()
 * @used-by df_quote_single()
 * @used-by \Df\Core\Text\Regex::isSubjectMultiline()
 * @used-by \Df\Xml\G::_p()
 */
function df_t():T {return T::s();}

/**
 * 2016-07-05
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop_2()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_rename()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_rename()
 * @used-by \Df\Framework\Form\Element\Multiselect::getElementHtml()
 * @used-by \Df\Sso\Button::attributes()
 * @used-by \Dfe\Moip\P\Reg::p()
 * @used-by \Dfe\Moip\Test\CaseT\Customer::pCustomer()
 * @used-by \Dfe\Moip\Test\CaseT\Notification::create()
 * @used-by \Dfe\Moip\Test\Order::pOrder()
 * @used-by \Dfe\Omise\Test\Customer::tRetrieveNonExistent()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\Sift\Session::get()
 * @used-by \Dfe\TBCBank\Charge::pCharge()
 * @used-by \Dfe\TBCBank\Test\CaseT\Regular::transId()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t02()
 * @param int|null $length [optional]	Длина уникальной части, без учёта $prefix.
 * @param string $prefix [optional]
 * @return string
 */
function df_uid($length = null, $prefix = '') {
	# Важно использовать $more_entropy = true, потому что иначе на быстрых серверах
	# (я заметил такое поведение при использовании Zend Server Enterprise и PHP 5.4)
	# uniqid будет иногда возвращать одинаковые значения при некоторых двух последовательных вызовах.
	# 2016-07-05
	# При параметре $more_entropy = true значение будет содержать точку,
	# например: «4b340550242239.64159797».
	# Решил сегодня удалять эту точку из-за платёжной системы allPay,
	# которая требует, чтобы идентификаторы содержали только цифры и латинские буквы.
	$r = str_replace('.', '', uniqid($prefix, $more_entropy = true)); /** @var string $r */
	# Уникальным является именно окончание uniqid, а не начало.
	# Два последовательных вызова uniqid могу вернуть:
	# 5233061890334
	# 52330618915dd
	# Начало у этих значений — одинаковое, а вот окончание — различное.
	return $prefix . (!$length ? $r : substr($r, -$length));
}