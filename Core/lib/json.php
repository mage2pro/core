<?php
use Df\Core\Exception as DFE;
/**
 * 2016-07-18
 * Видел решение здесь: http://stackoverflow.com/a/6041773
 * Но оно меня не устроило. И без собаки будет Warning.
 * @used-by df_check_json_complex()
 * @used-by \Df\API\Client::_p()
 * @param mixed $v
 */
function df_check_json($v):bool {return !is_null(@json_decode($v));}

/**
 * 2016-08-19
 * @see json_decode() спокойно принимает не только строки, но и числа, а также true.
 * Наша функция возвращает true, если аргумент является именно строкой.
 * @param mixed $v
 */
function df_check_json_complex($v):bool {return is_string($v) && df_starts_with($v, '{') && df_check_json($v);}

/**
 * «Returns the value encoded in json in appropriate PHP type.
 * Values true, false and null are returned as TRUE, FALSE and NULL respectively.
 * NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.»
 * http://php.net/manual/function.json-decode.php
 * @used-by df_cache_get_simple()
 * @used-by df_ci_get()
 * @used-by df_github_request()
 * @used-by df_http_json()
 * @used-by df_json_file_read()
 * @used-by df_json_prettify()
 * @used-by df_module_json()
 * @used-by df_oi_get()
 * @used-by df_oro_get_list()
 * @used-by df_package()
 * @used-by df_request_body_json()
 * @used-by df_stdclass_to_array()
 * @used-by df_test_file_lj()
 * @used-by dfp_container_get()
 * @used-by ikf_project()
 * @used-by wolf_customer_get()
 * @used-by wolf_sess_get()
 * @used-by \Df\API\Client::resJson()
 * @used-by \Df\Config\Backend\Serialized::valueUnserialize()
 * @used-by \Df\Config\Settings::json()
 * @used-by \Df\Framework\Form\Element\Fieldset::v()
 * @used-by \Df\GingerPaymentsBase\Api::req()
 * @used-by \Df\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Df\OAuth\App::requestToken()
 * @used-by \Df\OAuth\App::state()
 * @used-by \Df\Payment\W\Reader::testData()
 * @used-by \Df\Security\BlackList::load()
 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::http()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
 * @used-by \Dfe\CheckoutCom\Response::a()
 * @used-by \Dfe\Color\Plugin\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual::afterGetJsonConfig()
 * @used-by \Dfe\FacebookLogin\Customer::responseJson()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::afterCommitCallback()
 * @used-by \Dfe\TwoCheckout\Controller\Index\Index::paramsLocal
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
 * @used-by \Inkifi\Mediaclip\Event::s()
 * @used-by \Inkifi\Pwinty\Event::s()
 * @used-by \KingPalm\B2B\Block\RegionJS\Backend::_toHtml()
 * @used-by \KingPalm\B2B\Block\RegionJS\Frontend::_toHtml()
 * @used-by \MageWorx\OptionInventory\Controller\StockMessage\Update::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/125)
 * @used-by \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Grid::getSelectionAttributes() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/68)
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Helper\Data::getMediaClipProjects()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @used-by \TFC\Core\Plugin\Catalog\Block\Product\View\GalleryOptions::afterGetOptionsJson()
 * @used-by \VegAndTheCity\Core\Plugin\Mageplaza\Search\Helper\Data::afterGetProducts()
 * @used-by app/design/frontend/MageSuper/magestylish/Cart2Quote_Quotation/templates/email/proposal/items/quote/bundle.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/68)
 * @used-by app/design/frontend/MageSuper/magestylish/Cart2Quote_Quotation/templates/email/quote/items/quote/bundle.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/67)
 * @param string|null $s
 * @param bool $throw [optional]
 * @return array|mixed|bool|null
 * @throws DFE
 */
function df_json_decode($s, $throw = true) {/** @var mixed|bool|null $r */
	# 2015-12-19 У PHP 7.0.1 декодировании пустой строки почему-то приводит к сбою: «Decoding failed: Syntax error».
	# 2022-10-14
	# «an empty string is no longer considered valid JSON»:
	# https://www.php.net/manual/migration70.incompatible.php#migration70.incompatible.other.json-to-jsond
	if ('' === $s || is_null($s)) {
		$r = $s;
	}
	else {
		# 2016-10-30
		# json_decode('7700000000000000000000000') возвращает 7.7E+24
		# https://3v4l.org/NnUhk
		# http://stackoverflow.com/questions/28109419
		# Такие длинные числоподобные строки используются как идентификаторы КЛАДР
		# (модулем доставки «Деловые Линии»), и поэтому их нельзя так корёжить.
		# Поэтому используем константу JSON_BIGINT_AS_STRING
		# https://3v4l.org/vvFaF
		$r = json_decode($s, true, 512, JSON_BIGINT_AS_STRING);
		# 2016-10-28
		# json_encode(null) возвращает строку 'null', а json_decode('null') возвращает null.
		# Добавил проверку для этой ситуации, чтобы не считать её сбоем.
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
 * @used-by df_ci_add()
 * @used-by df_ejs()
 * @used-by df_js_x()
 * @used-by df_json_encode_partial()
 * @used-by df_json_prettify()
 * @used-by df_kv()
 * @used-by df_kv_table()
 * @used-by df_log_l()
 * @used-by df_oi_add()
 * @used-by df_widget()
 * @used-by dfp_container_add()
 * @used-by dfw_encode()
 * @used-by ikf_api_oi()
 * @used-by wolf_set()
 * @used-by \Df\API\Client::reqJson()
 * @used-by \Df\API\Response\Validator::long()
 * @used-by \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @used-by \Df\Config\Backend\Serialized::valueSerialize()
 * @used-by \Df\Core\O::j()
 * @used-by \Df\Framework\W\Result\Json::prepare()
 * @used-by \Df\GingerPaymentsBase\Api::req()
 * @used-by \Df\GoogleFont\Controller\Index\Index::execute()
 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
 * @used-by \Df\Qa\Failure\Error::preface()
 * @used-by \Df\Security\BlackList::save()
 * @used-by \Df\Sentry\Client::capture()
 * @used-by \Df\Sentry\Client::encode()
 * @used-by \Df\Sentry\Extra::adjust()
 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
 * @used-by \Dfe\BlackbaudNetCommunity\Customer::p()
 * @used-by \Dfe\Color\Plugin\Swatches\Model\Swatch::beforeBeforeSave()
 * @used-by \Dfe\Dynamics365\API\Validator\JSON::long()
 * @used-by \Dfe\Moip\API\Validator::long()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::afterLoad()
 * @used-by \Dfe\Square\API\Validator::long()
 * @used-by \Dfe\YandexKassa\Charge::pCharge()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @used-by \Inkifi\Mediaclip\T\CaseT\Order\Item::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Catalogue::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t02()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Get::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t03()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @used-by \TFC\Core\Plugin\Catalog\Block\Product\View\GalleryOptions::afterGetOptionsJson()
 * @used-by vendor/wolfautoparts.com/filter/view/frontend/templates/sidebar.phtml
 * @param mixed $v
 * @param int $flags [optional]
 */
function df_json_encode($v, $flags = 0):string {return json_encode(df_json_sort($v),
	JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE | $flags
);}

/**
 * 2020-02-15
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @param mixed $v
 * @return string
 */
function df_json_encode_partial($v) {return df_json_encode($v, JSON_PARTIAL_OUTPUT_ON_ERROR);}

/**
 * 2022-10-15
 * @used-by df_credentials()
 * @used-by df_module_json()
 * @used-by \Df\Payment\W\Reader::testData()
 * @used-by \Dfe\TwoCheckout\Controller\Index\Index::execute()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::j()
 * @param string $p
 * @return array|bool|mixed|null
 * @throws DFE
 */
function df_json_file_read($p) {return df_json_decode(df_file_read($p));}

/**
 * 2017-07-05
 * @used-by \Df\API\Client::_p()
 * @param string|array(string => mixed) $j
 * @return string
 */
function df_json_prettify($j) {return df_json_encode(df_json_decode($j));}

/**
 * 2017-09-07
 * I use @uses df_is_assoc() check,
 * because otherwise @uses df_ksort_r_ci() will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @used-by df_json_decode()
 * @used-by df_json_encode()
 * @param mixed $v
 * @return mixed
 */
function df_json_sort($v) {return !is_array($v) ? $v : df_ksort_r_ci($v);}