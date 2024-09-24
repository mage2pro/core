<?php
# 2023-07-13
# 1) [Magento 2.4.6] «Class "Zend_Filter_StringTrim" not found»: https://github.com/mage2pro/core/issues/216
# 2) "The Zend Framework 1 dependency has been removed from Magento 2 ≥ 2.4.6 since 2022-12-09": https://mage2.pro/t/6366
# 2023-07-14 «Replace Zend Framework 2 with Laminas»: https://github.com/mage2pro/core/issues/219
use Laminas\Filter\StringTrim as Trim;
/**
 * 2017-06-09
 * @used-by df_oqi_desc()
 * @used-by Df\Payment\Charge::text()
 * @used-by Df\Sentry\Serializer::chop()
 * @used-by Dfe\IPay88\Charge::pCharge()
 * @used-by Dfe\Qiwi\Charge::pBill()
 * @used-by Dfe\TwoCheckout\LineItem::adjustText()
 * @used-by Dfe\YandexKassa\Charge::pTaxLeaf()
 * @used-by Dfe\YandexKassa\Result::attributes()
 */
function df_chop(string $s, int $max = 0):string {return !$max || (mb_strlen($s = df_trim($s)) <= $max) ? $s :
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
 * @used-by df_chop()
 * @used-by df_csv_parse()
 * @used-by df_ejs()
 * @used-by df_est()
 * @used-by df_explode_n()
 * @used-by df_explode_space()
 * @used-by df_parse_colon()
 * @used-by df_string()
 * @used-by df_trim()
 * @used-by df_trim_ds()
 * @used-by df_xml_x()
 * @used-by Df\Config\Source\LetterCase::apply()
 * @used-by Df\Core\Html\Tag::content()
 * @used-by Df\Framework\Form\Element::uidSt()
 * @used-by Df\Qa\Dumper::dumpS()
 * @used-by Df\Qa\Failure::sections()
 * @used-by Dfe\Portal\Block\Content::getTemplate()
 * @used-by Dfe\Portal\Controller\Index\Index::execute()
 * @used-by Dfe\Portal\Router::match()
 * @used-by Dfe\TwitterTimeline\Block::_toHtml()
 * @used-by Dfe\TwoCheckout\Address::line()
 * @used-by Dfe\TwoCheckout\Method::_refund()
 * @used-by Inkifi\Core\Plugin\Catalog\Block\Product\View::afterSetLayout()
 * @param string|string[] $s
 * @param string $charlist [optional]
 * @param bool|mixed|Closure $throw [optional]
 * @return string|string[]
 */
function df_trim($s, $charlist = null, $throw = false) {return df_try(function() use($s, $charlist, $throw) {
	/** @var string|string[] $r */
	if (is_array($s)) {
		$r = df_map('df_trim', $s, [$charlist, $throw]); /** @uses df_trim() */
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
		$filter = new Trim($charlist); /** @var Trim $filter */
		$r = $filter->filter($s);
		/**
		 * @see Zend_Filter_StringTrim::filter() теоретически может вернуть null,
		 * потому что этот метод зачастую перепоручает вычисление результата функции @uses preg_replace()
		 * @url https://php.net/manual/function.preg-replace.php
		 */
		$r = df_nts($r);
		# Как ни странно, Zend_Filter_StringTrim иногда выдаёт результат « ».
		if (' ' === $r) {
			$r = '';
		}
	}
	return $r;
}, false === $throw ? $s : $throw);}

/**
 * Пусть пока будет так. Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses ltrim()
 * @used-by df_trim_ds_left()
 * @used-by Df\Config\Settings::phpNameToKey()
 * @used-by Dfe\PostFinance\W\Event::cardNumber()
 */
function df_trim_left(string $s, string $charlist = ''):string {return ltrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * Пусть пока будет так. Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses rtrim()
 * @used-by df_chop()            
 * @used-by df_file_ext_def()
 * @used-by df_trim_ds_right()
 */
function df_trim_right(string $s, string $charlist = ''):string {return rtrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * It chops the $trim prefix or/and suffix from the $s string.
 * 2016-10-28 It now supports multiple $trim.
 * 2020-06-28 @deprecated It is unused.
 * @param string|string[] $trim
 */
function df_trim_text(string $s, $trim):string {return df_trim_text_left_right($s, $trim, $trim);}

/**
 * 2016-10-28
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by df_trim_text_left()
 * @used-by df_trim_text_right()
 * @param string[] $trimA
 */
function df_trim_text_a(string $s, array $trimA, callable $f):string {
	$r = $s; /** @var string $r */
	$l = mb_strlen($r); /** @var int $l */
	foreach ($trimA as $trim) {/** @var string $trim */
		if ($l !== mb_strlen($r = call_user_func($f, $r, $trim))) {
			break;
		}
	}
	return $r;
}

/**
 * It chops the $trim prefix from the $s string.
 * 2016-10-28 It now supports multiple $trim.
 * @see df_prepend()
 * @see df_starts_with()
 * @used-by df_domain()
 * @used-by df_domain_current()
 * @used-by df_magento_version()
 * @used-by df_magento_version_remote()
 * @used-by df_media_url2path()
 * @used-by df_module_name_by_path()
 * @used-by df_oqi_amount()
 * @used-by df_path_rel()
 * @used-by df_product_image_path2rel()
 * @used-by df_replace_store_code_in_url()
 * @used-by df_trim_text_left()
 * @used-by df_trim_text_left_right()
 * @used-by dfpm_code_short()
 * @used-by dfsm_code_short()
 * @used-by Df\Framework\Request::extra()
 * @used-by Df\PaypalClone\Signer::_sign()
 * @used-by Df\Qa\Trace\Frame::__toString()
 * @used-by Dfe\Qiwi\W\Event::pid()
 * @used-by Dfe\Stripe\Facade\Token::trimmed()
 * @used-by Dfe\TwitterTimeline\Block::_toHtml()
 * @used-by Dfe\Zoho\App::title()
 * @used-by CabinetsBay\Catalog\B\Category::images() (https://github.com/cabinetsbay/catalog/issues/2)
 * @param string|string[] $s
 * @param string|string[] $trim
 * @return string|string[]
 */
function df_trim_text_left($s, $trim) {return is_array($s) ? df_map(__FUNCTION__, $s, [$trim]) : (
	is_array($trim) ? df_trim_text_a($s, $trim, __FUNCTION__) : (
		$trim === mb_substr($s, 0, $l = mb_strlen($trim)) ? mb_substr($s, $l) : $s
	)
);}

/**
 * 2021-12-12
 * @used-by df_trim_text()
 * @used-by Df\Core\Text\Marker::unmark()
 * @used-by Dfe\TwitterTimeline\Block::_toHtml()
 */
function df_trim_text_left_right(string $s, string $left, string $right):string {return df_trim_text_right(
	df_trim_text_left($s, $left), $right
);}

/**
 * It chops the $trim ending from the $s string.
 * 2016-10-28 It now supports multiple $trim.
 * @see df_ends_with()
 * @see df_append()
 * @used-by df_oqi_amount()
 * @used-by df_trim_interceptor()
 * @used-by df_trim_text_left_right()
 * @used-by df_trim_text_right()
 * @used-by dfe_portal_stripe_customers()
 * @used-by Df\Framework\Form\Element\Fieldset::nameFull()
 * @used-by Dfe\Oro\Test\Basic::t02_orders_stripe()
 * @used-by Dfe\TwitterTimeline\Block::_toHtml()
 * @param string|string[] $s
 * @param string|string[] $trim
 * @return string|string[]
 */
function df_trim_text_right($s, $trim) {return is_array($s) ? df_map(__FUNCTION__, $s, [$trim]) :(
	is_array($trim) ? df_trim_text_a($s, $trim, __FUNCTION__) : (
		0 !== ($l = mb_strlen($trim)) && $trim === mb_substr($s, -$l) ? mb_substr($s, 0, -$l) : $s
	)
);}