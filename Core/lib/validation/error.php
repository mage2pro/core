<?php
use Df\Core\Exception as DFE;
use Exception as E;
use Magento\Framework\Phrase;

/**
 * 2016-08-27
 * Параметр $caller нам реально нужен,
 * потому что посредством @see debug_backtrace() мы можем получить только имя того класса,
 * где абстрактный метод был объявлен, а нам нужно имя класса текущего объекта
 * (в классе которого абстрактный метод должен был быть перекрыт).
 * @used-by \Df\Payment\Block\Info::ciId()
 * @param object|string $caller
 */
function df_abstract($caller) {
	/**
	 * 2017-11-19
	 * "Improve the «The method \Df\Payment\Block\Info::prepare() should be redefined
	 * by the \Df\Payment\Block\Info class» message": https://github.com/mage2pro/core/issues/56
	 * @var string $callerC
	 */
	if (($callerC = df_cts($caller)) === df_caller_c())  {
		df_error_html(
			"The $callerC class is abstract: you should redefine at least the %s method.", df_caller_ml()
		);
	}
	df_error_html("The method %s should be redefined by the <b>$callerC</b> class.", df_caller_mh());
}

/**
 * @used-by df_address_is_billing()
 * @used-by df_ar()
 * @used-by df_assert()
 * @used-by df_assert_assoc()
 * @used-by df_assert_callable()
 * @used-by df_assert_class_exists()
 * @used-by df_assert_eq()
 * @used-by df_assert_gd()
 * @used-by df_assert_ge()
 * @used-by df_assert_gt()
 * @used-by df_assert_https()
 * @used-by df_assert_in()
 * @used-by df_assert_le()
 * @used-by df_assert_leaf()
 * @used-by df_assert_lt()
 * @used-by df_assert_ne()
 * @used-by df_assert_nef()
 * @used-by df_assert_traversable()
 * @used-by df_bool()
 * @used-by df_call()
 * @used-by df_caller_mm()
 * @used-by df_con_hier_suf()
 * @used-by df_con_hier_suf_ta()
 * @used-by df_con_s()
 * @used-by df_country()
 * @used-by df_country_ctn()
 * @used-by df_customer()
 * @used-by df_date_from_db()
 * @used-by df_extend()
 * @used-by df_fe_m()
 * @used-by df_file_name()
 * @used-by df_float()
 * @used-by df_int()
 * @used-by df_invoice_by_trans()
 * @used-by df_json_decode()
 * @used-by df_leaf_sne()
 * @used-by df_load()
 * @used-by df_mail_shipment()
 * @used-by df_module_file()
 * @used-by df_not_implemented()
 * @used-by df_oq()
 * @used-by df_oq_currency_c()
 * @used-by df_oq_sa()
 * @used-by df_oq_shipping_amount()
 * @used-by df_oq_shipping_desc()
 * @used-by df_oqi_is_leaf()
 * @used-by df_oqi_qty()
 * @used-by df_order()
 * @used-by df_order_last()
 * @used-by df_oro_get_list()
 * @used-by df_pad()
 * @used-by df_product_current()
 * @used-by df_route()
 * @used-by df_sentry_m()
 * @used-by df_sprintf_strict()
 * @used-by df_string()
 * @used-by df_try()
 * @used-by df_xml_children()
 * @used-by df_xml_parse()
 * @used-by df_xml_throw_last()
 * @used-by dfaf()
 * @used-by dfc()
 * @used-by dfp()
 * @used-by dfp_due()
 * @used-by dfp_oq()
 * @used-by dfp_refund()
 * @used-by dfpex_args()
 * @used-by dfpm()
 * @used-by dfs_con()
 * @used-by ikf_api_oi()
 * @used-by \Df\API\Settings::key()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Core\Helper\Text::quote()
 * @used-by \Df\Core\OLegacy::cacheKeyPerStore()
 * @used-by \Df\Core\R\ConT::generic()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Core\Validator::byName()
 * @used-by \Df\Core\Validator::check()
 * @used-by \Df\Core\Validator::checkProperty()
 * @used-by \Df\Core\Validator::resolve()
 * @used-by \Df\Framework\Form\Element\Text::getValue()
 * @used-by \Df\Geo\Client::onError()
 * @used-by \Df\GingerPaymentsBase\Api::req()
 * @used-by \Df\Payment\BankCardNetworks::url()
 * @used-by \Df\Payment\Method::getInfoBlockType()
 * @used-by \Df\Payment\Method::getInfoInstance()
 * @used-by \Df\Payment\Method::s()
 * @used-by \Df\Payment\Source\Identification::get()
 * @used-by \Df\Payment\TID::i2e()
 * @used-by \Df\Payment\TM::tReq()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\F::c()
 * @used-by \Df\Payment\W\Nav::p()
 * @used-by \Df\Payment\W\Reader::testData()
 * @used-by \Df\PaypalClone\W\Event::validate()
 * @used-by \Df\Qa\Message\Failure\Error::throwLast()
 * @used-by \Df\Qa\Method::throwException()
 * @used-by \Df\Qa\State::methodParameter()
 * @used-by \Df\Shipping\Method::s()
 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
 * @used-by \Df\StripeClone\Facade\Charge::cardData()
 * @used-by \Df\Xml\Parser\Entity::descendS()
 * @used-by \Df\Xml\Parser\Entity::descendWithCast()
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Df\Xml\X::addChild()
 * @used-by \Df\Xml\X::importString()
 * @used-by \Df\Zf\Filter\StringT\Trim::filter()
 * @used-by \Df\Zf\Filter\StringTrim::_splitUtf8()
 * @used-by \Df\Zf\Validate\Boolean::filter()
 * @used-by \Df\Zf\Validate\FloatT::filter()
 * @used-by \Df\Zf\Validate\IntT::filter()
 * @used-by \Df\Zf\Validate\Nat0::filter()
 * @used-by \Df\Zf\Validate\Nat::filter()
 * @used-by \Dfe\AmazonLogin\Customer::validate()
 * @used-by \Dfe\BlackbaudNetCommunity\Customer::p()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\Sift\Controller\Index\Index::checkSignature()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @used-by \Dfr\Core\Dictionary::e()
 * @used-by \Dfr\Core\Realtime\Dictionary::translate()
 * @used-by \Inkifi\Mediaclip\Event::oi()
 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
 * @used-by \Justuno\M2\Response::p()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @used-by \Mangoit\MediaclipHub\Model\Orders::byOId()
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 * @used-by \RWCandy\Captcha\Observer\CustomerSaveBefore::execute()
 * @param array ...$args
 * @throws DFE
 */
function df_error(...$args) {
	df_header_utf();
	$e = df_error_create(...$args); /** @var DFE $e */
	/**
	 * 2020-02-15
	 * 1) "The Cron log (`magento.cron.log`) should contain a backtrace for every exception logged":
	 * https://github.com/tradefurniturecompany/site/issues/34
	 * 2) The @see \Exception 's backtrace is set when the exception is created, not when it is thrown:
	 * https://3v4l.org/qhd7m
	 * So we have a correct backtrace even without throwing the exception.
	 * 2020-02-17 @see \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
	 */
	if (df_is_cron()) {
		df_log_e($e, 'Df_Cron');
	}
	throw $e;
}

/**
 * 2016-07-31
 * @used-by df_error()
 * @used-by df_error_create_html()
 * @used-by \Df\API\Client::_p()
 * @param string|string[]|mixed|E|Phrase|null $m [optional]
 * @return DFE
 */
function df_error_create($m = null) {return
	$m instanceof E ? df_ewrap($m) :
		new DFE($m instanceof Phrase ? $m : (
			/**
			 * 2019-12-16
			 * I have changed `!$m` to `is_null($m)`.
			 * It passes an empty string ('') directly to @uses \Df\Core\Exception::__construct()
			 * and it prevents @uses \Df\Core\Exception::__construct() from calling @see df_bt()
			 * @see \Df\Core\Exception::__construct():
			 *		if (is_null($m)) {
			 *			$m = __($prev ? df_ets($prev) : 'No message');
			 *			// 2017-02-20 To facilite the «No message» diagnostics.
			 *			if (!$prev) {
			 *				df_bt();
			 *			}
			 *		}
			 */
			is_null($m) ? null : (is_array($m) ? implode("\n\n", $m) : (
				df_contains($m, '%1') ? __($m, ...df_tail(func_get_args())) :
					df_format(func_get_args())
			))
		))
;}

/**
 * 2016-08-02
 * @param array ...$args
 * @return DFE
 */
function df_error_create_html(...$args) {return df_error_create(...$args)->markMessageAsHtml(true);}

/**
 * 2016-07-31
 * @used-by df_abstract()
 * @used-by df_assert_not_closure()
 * @used-by df_config_e()
 * @used-by df_should_not_be_here()
 * @used-by \Df\OAuth\App::validateResponse()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @param array ...$args
 * @throws DFE
 */
function df_error_html(...$args) {df_header_utf(); throw df_error_create_html(...$args);}

/**
 * 2016-07-27
 * @see df_should_not_be_here()
 * @param string $method
 * @throws DFE
 */
function df_not_implemented($method) {df_error("The method «{$method}» is not implemented yet.");}

/**
 * @see df_not_implemented()
 * @throws DFE
 */
function df_should_not_be_here() {df_error_html('The method %s is not allowed to call.', df_caller_mh());}

/**
 * Эта функция используется, как правило, при отключенном режиме разработчика.
 * @see mageCoreErrorHandler():
 *		if (Mage::getIsDeveloperMode()) {
 *			throw new Exception($errorMessage);
 *		}
 *		else {
 *			Mage::log($errorMessage, Zend_Log::ERR);
 *		}
 * @param bool $r [optional]
 * @throws DFE
 */
function df_throw_last_error($r = false) {$r ?: \Df\Qa\Message\Failure\Error::throwLast();}