<?php
use Df\Core\Exception as DFE;
/**
 * 2016-07-18
 * Видел решение здесь: http://stackoverflow.com/a/6041773
 * Но оно меня не устроило.
 * И без собаки будет Warning.
 * @param mixed $v
 * @return bool
 */
function df_check_json($v) {/** @noinspection PhpUsageOfSilenceOperatorInspection */ return !is_null(
	@json_decode($v)
);}

/**
 * 2016-08-19
 * @see json_decode() спокойно принимает не только строки, но и числа, а также true.
 * Наша функция возвращает true, если аргумент является именно строкой.
 * @param mixed $v
 * @return bool
 */
function df_check_json_complex($v) {return is_string($v) && df_starts_with($v, '{') && df_check_json($v);}

/**
 * @used-by df_ci_get()
 * @used-by df_credentials()
 * @used-by df_github_request()
 * @used-by df_http_json()
 * @used-by df_json_prettify()
 * @used-by df_module_json()
 * @used-by df_oi_get()
 * @used-by df_oro_get_list()
 * @used-by df_package()
 * @used-by df_request_body_json()
 * @used-by df_stdclass_to_array()
 * @used-by df_test_file_lj()
 * @used-by df_unserialize_simple()
 * @used-by dfp_container_get()
 * @used-by \Df\API\Client::resJson()
 * @used-by \Df\Config\Backend\Serialized::valueUnserialize()
 * @used-by \Df\Config\Settings::json()
 * @used-by \Df\Framework\Form\Element\Fieldset::v()
 * @used-by \Df\GingerPaymentsBase\Api::req()
 * @used-by \Df\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Df\OAuth\App::requestToken()
 * @used-by \Df\OAuth\App::state()
 * @used-by \Df\Payment\W\Reader::testData()
 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::http()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
 * @used-by \Dfe\CheckoutCom\Response::a()
 * @used-by \Dfe\FacebookLogin\Customer::responseJson()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::afterCommitCallback()
 * @used-by \Dfe\TwoCheckout\Controller\Index\Index::paramsLocal
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @param $s|null $string
 * @param bool $throw [optional]
 * @return array|mixed|bool|null
 * @throws DFE
 * Returns the value encoded in json in appropriate PHP type.
 * Values true, false and null are returned as TRUE, FALSE and NULL respectively.
 * NULL is returned if the json cannot be decoded
 * or if the encoded data is deeper than the recursion limit.
 * http://php.net/manual/function.json-decode.php
 */
function df_json_decode($s, $throw = true) {
	/** @var mixed|bool|null $r */
	// 2015-12-19
	// У PHP 7.0.1 декодировании пустой строки почему-то приводит к сбою: «Decoding failed: Syntax error».
	if ('' === $s || is_null($s)) {
		$r = $s;
	}
	else {
		// 2016-10-30
		// json_decode('7700000000000000000000000') возвращает 7.7E+24
		// https://3v4l.org/NnUhk
		// http://stackoverflow.com/questions/28109419
		// Такие длинные числоподобные строки используются как идентификаторы КЛАДР
		// (модулем доставки «Деловые Линии»), и поэтому их нельзя так корёжить.
		// Поэтому используем константу JSON_BIGINT_AS_STRING
		// https://3v4l.org/vvFaF
		$r = json_decode($s, true, 512, JSON_BIGINT_AS_STRING);
		// 2016-10-28
		// json_encode(null) возвращает строку 'null',
		// а json_decode('null') возвращает null.
		// Добавил проверку для этой ситуации, чтобы не считать её сбоем.
		if (is_null($r) && 'null' !== $s && $throw) {
			df_assert_ne(JSON_ERROR_NONE, json_last_error());
			df_error(
				"Parsing a JSON document failed with the message «%s».\nThe document:\n{$s}"
				,json_last_error_msg()
			);
		}
	}
	return df_json_sort($r);
}

/**
 * 2015-12-06
 * @used-by df_cc_kv()
 * @used-by df_ci_add()
 * @used-by df_js()
 * @used-by df_json_prettify()
 * @used-by df_log_l()
 * @used-by df_oi_add()
 * @used-by df_widget()
 * @used-by dfp_container_add()
 * @used-by dfw_encode()
 * @used-by \Df\API\Client::reqJson()
 * @used-by \Df\API\Document::j()
 * @used-by \Df\API\Response\Validator::long()
 * @used-by \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @used-by \Df\Config\Backend\Serialized::valueSerialize()
 * @used-by \Df\Framework\W\Result\Json::prepare()
 * @used-by \Df\GingerPaymentsBase\Api::req()
 * @used-by \Df\GoogleFont\Controller\Index\Index::execute()
 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
 * @used-by \Df\Sentry\Client::capture()
 * @used-by \Df\Sentry\Extra::adjust()
 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
 * @used-by \Dfe\BlackbaudNetCommunity\Customer::p()
 * @used-by \Dfe\Dynamics365\API\Validator\JSON::long()
 * @used-by \Dfe\Moip\API\Validator::long()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::afterLoad()
 * @used-by \Dfe\Square\API\Validator::long()
 * @used-by \Dfe\YandexKassa\Charge::pCharge()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @param mixed $v
 * @return string
 */
function df_json_encode($v) {return json_encode(df_json_sort($v),
	JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
);}

/**
 * 2017-07-05
 * @param string|array(string => mixed) $j
 * @return string
 */
function df_json_prettify($j) {return df_json_encode(df_json_decode($j));}

/**
 * 2017-09-07
 * I use the @uses df_is_assoc() check,
 * because otherwise @uses df_ksort_r_ci() will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @used-by df_json_decode()
 * @used-by df_json_encode()
 * @param mixed $v
 * @return mixed
 */
function df_json_sort($v) {return !is_array($v) ? $v : (df_is_assoc($v) ? df_ksort_r_ci($v) :
	/**
	 * 2017-09-08
	 * @todo It would be nice to use df_sort($v) here,
	 * but now it will break the «Sales Documents Numeration» extension,
	 * because @see \Df\Config\Settings::_matrix() relies on an exact items ordering, e.g:
	 * [["ORD-{Y/m}-",null],["INV-",null],["SHIP-{Y-M}",null],["RET-{STORE-ID}-",null]]
	 * If we reorder these values, the «Sales Documents Numeration» extension will work incorrectly.
	 * I need to think how to improve it.
	 */
	$v
);}