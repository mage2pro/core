<?php
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\RequestInterface as IRequest;
# 2018-07-25 https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Helper/PostHelper.php
use Magento\Framework\Data\Helper\PostHelper;

/**
 * 2015-01-28
 * 2016-07-31
 * К сожалению, мы не можем указывать кодировку в обработчике, установленном @see set_exception_handler(),
 * потому что @see set_exception_handler() в Magento работать не будет
 * из-за глобального try..catch в методе @see Mage::run()
 * @used-by df_error()
 * @used-by df_error_html()
 */
function df_header_utf():void {df_is_cli() || headers_sent() ?: header('Content-Type: text/html; charset=UTF-8');}

/**
 * 2017-02-26      
 * @used-by df_response_sign()
 * @used-by \Df\GingerPaymentsBase\Api::__construct()
 * @param array(string => string) $a [optional]
 * @return array(string => string)
 */
function df_headers(array $a = []):array {return dfak_transform($a + [
	'Author' => 'Dmitry Fedyuk', 'EMail' => 'admin@mage2.pro', 'Website' => 'https://mage2.pro'
], function($k) {return "X-Mage2.PRO-{$k}";});}

/**             
 * 2016-12-04
 * @used-by df_customer_logged_in_2()
 * @used-by \Wolf\Filter\Block\Navigation::getCacheKeyInfo()
 */
function df_http_context():Context {return df_o(Context::class);}

/**
 * 2015-11-27
 * Note 1.
 * Google API в случае сбоя возвращает корректный JSON, но с кодом HTTP 403,
 * что приводит к тому, что @uses file_get_contents() не просто возвращает JSON,
 * а создаёт при этом @see E_WARNING.
 * Чтобы при коде 403 warning не создавался, использую ключ «ignore_errors»:
 * https://php.net/manual/context.http.php#context.http.ignore-errors
 * http://stackoverflow.com/a/21976746
 * Note 2.
 * Обратите внимание, что для использования @uses file_get_contents
 * с адресами https требуется расширение php_openssl интерпретатора PHP,
 * однако оно является системным требованием Magento 2:
 * http://devdocs.magento.com/guides/v2.0/install-gde/system-requirements.html#required-php-extensions
 * Поэтому мы вправе использовать здесь @uses file_get_contents
 * Note 3. The function returns the read data or FALSE on failure. https://php.net/manual/function.file-get-contents.php
 * 2016-05-31
 * Стандартное время ожидание ответа сервера задаётся опцией default_socket_timeout:
 * https://php.net/manual/filesystem.configuration.php#ini.default-socket-timeout
 * Её значение по-умолчанию равно 60 секундам.
 * Конечно, при оформлении заказа негоже заставлять покупателя ждать 60 секунд
 * только ради узнавания его страны вызовом @see df_visitor()
 * Поэтому добавил возможность задавать нестандартное время ожидания ответа сервера:
 * http://stackoverflow.com/a/10236480
 * https://amitabhkant.com/2011/08/21/using-timeouts-with-file_get_contents-in-php/
 * @used-by df_http_json()
 * @used-by \Df\GoogleFont\Fonts::responseA()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 *
 * @param array(string => string) $params [optional]
 */
function df_http_get(string $urlBase, array $params = [], int $timeout = 0):string {
	$url = !$params ? $urlBase : $urlBase . '?' . http_build_query($params); /** @var string $url */
	/**
	 * 2016-05-31
	 * file_get_contents() может возбудить @see E_WARNING:
	 * «failed to open stream: A connection attempt failed
	 * because the connected party did not properly respond after a period of time,
	 * or established connection failed because connected host has failed to respond.»
	 */
	return df_assert_ne(false, @file_get_contents($url, null, stream_context_create(['http' => df_clean([
		'ignore_errors' => true, 'timeout' => $timeout
	])])));
}

/**
 * 2016-04-13
 * @see df_request_body_json()
 * @used-by \Df\Core\Visitor::responseA()
 * @used-by \Dfe\AmazonLogin\Customer::res()
 * @used-by \Dfe\CurrencyConvert\Ecb::rates()
 * @used-by \Dfe\GoogleBackendLogin\Plugin\Backend\App\AbstractAction::beforeDispatch()
 * @used-by \Dfe\Paymill\Test\CaseT::token()
 * @used-by \Dfe\Salesforce\Test\Basic::t02_the_latest_version()
 * @param string $urlBase
 * @param array(string => string) $params [optional]
 * @param int|null $timeout [optional]
 * @return array(string => mixed)
 */
function df_http_json($urlBase, array $params = [], $timeout = null):array {return
	/** @var string|bool $json */ /** @var bool|array|null $r */
	false === ($json = df_http_get($urlBase, $params, $timeout))
	|| !is_array($r = df_json_decode($json))
	? [] : $r
;}

/**
 * 2018-07-25
 * @used-by \Frugue\Store\Block\Switcher::post()
 * @used-by vendor/blushme/checkout/view/frontend/templates/extra-sell.phtml (blushme.se)
 */
function df_post_h():PostHelper {return df_o(PostHelper::class);}

/**
 * @used-by df_scope()
 * @used-by df_order()
 * @used-by df_store()
 * @used-by \Alignet\Paymecheckout\Plugin\Magento\Framework\Session\SidResolver::aroundGetSid() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Framework\App\Http::aroundLaunch() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/72)
 * @used-by \Df\Backend\Model\Auth::loginByEmail()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\Fieldset::beforeAddField()
 * @used-by \Df\Framework\Request::clean()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\Framework\Request::extraKeysRaw()
 * @used-by \Df\GoogleFont\Controller\Index\Index::execute()
 * @used-by \Df\GoogleFont\Controller\Index\Preview::contents()
 * @used-by \Df\GoogleFont\Font\Variant\Preview\Params::fromRequest()
 * @used-by \Df\OAuth\App::getAndSaveTheRefreshToken()
 * @used-by \Df\OAuth\App::state()
 * @used-by \Df\OAuth\ReturnT::redirectUrl()
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Df\Payment\CustomerReturn::isSuccess()
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @used-by \Dfe\AllPay\Controller\CustomerReturn\Index::message()
 * @used-by \Dfe\AmazonLogin\Customer::url()
 * @used-by \Dfe\AmazonLogin\Customer::validate()
 * @used-by \Dfe\BlackbaudNetCommunity\Customer::p()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::execute()
 * @used-by \Dfe\FacebookLogin\Customer::appScopedId()
 * @used-by \Dfe\FacebookLogin\Customer::token()
 * @used-by \Dfe\IPay88\Controller\CustomerReturn\Index::isSuccess()
 * @used-by \Dfe\IPay88\Controller\CustomerReturn\Index::message()
 * @used-by \Dfe\Robokassa\Controller\CustomerReturn\Index::isSuccess()
 * @used-by \Dfe\Sift\Observer\Customer\SaveAfterDataObject::execute()
 * @used-by \Dfe\Stripe\Controller\CustomerReturn\Index::isSuccess()
 * @used-by \Doormall\Shipping\Controller\Index\Index::execute()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @used-by \Inkifi\Consolidation\Controller\Adminhtml\Index\Index::execute()
 * @used-by \Inkifi\Consolidation\Processor::s()
 * @used-by \MageWorx\OptionInventory\Controller\StockMessage\Update::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/125)
 * @used-by \PPCs\Core\Plugin\Iksanika\Stockmanage\Block\Adminhtml\Product\Grid::aroundAddColumn()
 * @used-by \PPCs\Core\Plugin\Iksanika\Stockmanage\Controller\Adminhtml\Product\MassUpdateProducts::beforeExecute()
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 * @used-by \Wolf\Filter\Block\Navigation::selectedPath()
 * @used-by \Wolf\Filter\Controller\Garage\Remove::execute()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 * @param string|string[]|null $k [optional]
 * @param string|null|callable $d [optional]
 * @return string|array(string => string)
 */
function df_request($k = null, $d = null) {$o = df_request_o(); return is_null($k) ? $o->getParams() : (
	is_array($k) ? dfa($o->getParams(), $k) : df_if1(is_null($r = $o->getParam($k)) || '' === $r, $d, $r)
);}

/**              
 * 2017-03-09
 * @used-by df_request_body_json()
 * @used-by \Dfe\Sift\Controller\Index\Index::checkSignature()
 */
function df_request_body():string {return df_assert_ne(false, file_get_contents('php://input'));}

/**
 * 2017-03-09
 * @see df_http_json()
 * @used-by \Df\Payment\W\Reader\Json::http()
 */
function df_request_body_json():string {return !($j = df_request_body()) ? [] : df_json_decode($j);}

/**
 * 2016-12-25
 * The @uses \Zend\Http\Request::getHeader() method is insensitive to the argument's letter case:
 * @see \Zend\Http\Headers::createKey()
 * https://github.com/zendframework/zendframework/blob/release-2.4.6/library/Zend/Http/Headers.php#L462-L471
 * @used-by df_request_ua()
 * @used-by \Dfe\Qiwi\W\Reader::http()
 * @used-by \Dfe\Sift\Controller\Index\Index::checkSignature()
 * @param string $k
 * @return string|false
 */
function df_request_header($k) {return df_request_o()->getHeader($k);}

/**
 * 2021-06-05
 * @used-by df_context()
 * @used-by \Df\Sentry\Client::get_http_data()
 */
function df_request_method():string {return dfa($_SERVER, 'REQUEST_METHOD');}

/**
 * 2015-08-14 https://github.com/magento/magento2/issues/1675
 * @used-by df_action_name()
 * @used-by df_context()
 * @used-by df_is_ajax()
 * @used-by df_request()
 * @used-by df_request_header()
 * @used-by df_rp_has()
 * @used-by \Alignet\Paymecheckout\Plugin\Magento\Framework\Session\SidResolver::aroundGetSid() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Framework\App\Http::aroundLaunch() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/72)
 * @used-by \Df\Framework\Action::module()
 * @used-by \Df\Sentry\Client::get_http_data()
 * @used-by \Dfe\Portal\Block\Content::getTemplate()
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 * @used-by \MageWorx\OptionInventory\Controller\StockMessage\Update::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/125)
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @return IRequest|RequestHttp
 */
function df_request_o() {return df_o(IRequest::class);}

/**
 * 2022-02-23
 * 1) Sometimes @see df_action_has() does not work because the following methods are not yet called by Magento:
 * @see \Magento\Framework\App\Request\Http::setRouteName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setActionName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setControllerName()
 * In this case, use df_rp_has().
 * 2) @uses \Magento\Framework\App\Request\Http::getPathInfo() starts with `/`.
 * 3) Synonym: @see df_url_path_contains()
 * 4) `df_request_o()->getPathInfo()` seems to be the same as `dfa($_SERVER, 'REQUEST_URI')`:
 * 5) 2018-05-11
 * df_contains(df_url(), $s)) does not work properly for some requests.
 * E.g.: df_url() for the `/us/stores/store/switch/___store/uk` request will return `<website>/us/`
 * @used-by df_url_path_contains()
 * @used-by \Alignet\Paymecheckout\Plugin\Magento\Framework\Session\SidResolver::aroundGetSid() (innomuebles.com)
 * @param string ...$s
 */
function df_rp_has(...$s):bool {return df_contains(df_request_o()->getPathInfo(), ...$s);}