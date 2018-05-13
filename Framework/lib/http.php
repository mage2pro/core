<?php
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\RequestInterface as IRequest;

/**
 * 2015-01-28
 * По примеру @see df_handle_entry_point_exception()
 * добавил условие @uses Mage::getIsDeveloperMode()
 * потому что Magento выводит диагностические сообщения на экран
 * только при соблюдении этого условия.
 * 2016-07-31
 * К сожалению, мы не можем указывать кодировку в обработчике,
 * установленном @see set_exception_handler(),
 * потому что @see set_exception_handler() в Magento работать не будет
 * из-за глобального try..catch в методе @see Mage::run()
 * @used-by df_error()
 * @used-by df_error_html()
 */
function df_header_utf() {headers_sent() ?: header('Content-Type: text/html; charset=UTF-8');}

/**
 * 2017-02-26      
 * @used-by df_response_sign()
 * @used-by \Df\GingerPaymentsBase\Api::__construct()
 * @param array(string => string) $a [optional]
 * @return array(string => string)
 */
function df_headers(array $a = []) {return dfa_key_transform($a + [
	'Author' => 'Dmitry Fedyuk', 'EMail' => 'admin@mage2.pro', 'Website' => 'https://mage2.pro'
], function($k) {return "X-Mage2.PRO-{$k}";});}

/**             
 * 2016-12-04
 * @used-by df_customer_logged_in_2()
 * @return Context
 */
function df_http_context() {return df_o(Context::class);}

/**
 * 2015-11-27
 * Note 1.
 * Google API в случае сбоя возвращает корректный JSON, но с кодом HTTP 403,
 * что приводит к тому, что @see file_get_contents() не просто возвращает JSON,
 * а создаёт при этом warning.
 * Чтобы при коде 403 warning не создавался, использую ключ «ignore_errors»:
 * http://php.net/manual/en/context.http.php#context.http.ignore-errors
 * http://stackoverflow.com/a/21976746
 * Note 2.
 * Обратите внимание, что для использования @uses file_get_contents
 * с адресами https требуется расширение php_openssl интерпретатора PHP,
 * однако оно является системным требованием Magento 2:
 * http://devdocs.magento.com/guides/v2.0/install-gde/system-requirements.html#required-php-extensions
 * Поэтому мы вправе использовать здесь @uses file_get_contents
 * Note 3.
 * The function returns the read data or FALSE on failure.
 * http://php.net/manual/function.file-get-contents.php
 *
 * 2016-05-31
 * Стандартное время ожидание ответа сервера задаётся опцией default_socket_timeout:
 * http://php.net/manual/en/filesystem.configuration.php#ini.default-socket-timeout
 * Её значение по-умолчанию равно 60 секундам.
 * Конечно, при оформлении заказа негоже заставлять покупателя ждать 60 секунд
 * только ради узнавания его страны вызовом @see df_visitor()
 * Поэтому добавил возможность задавать нестандартное время ожидания ответа сервера:
 * http://stackoverflow.com/a/10236480
 * https://amitabhkant.com/2011/08/21/using-timeouts-with-file_get_contents-in-php/
 *
 * @used-by df_http_json()
 * @used-by \Df\GoogleFont\Fonts::responseA()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 *
 * @param $urlBase
 * @param array(string => string) $params [optional]
 * @param int|null $timeout [optional]
 * @return string|bool
 */
function df_http_get($urlBase, array $params = [], $timeout = null) {
	/** @var string $url */
	$url = !$params ? $urlBase : $urlBase . '?' . http_build_query($params);
	/**
	 * 2016-05-31
	 * @uses file_get_contents() может возбудить Warning:
	 * «failed to open stream: A connection attempt failed
	 * because the connected party did not properly respond after a period of time,
	 * or established connection failed because connected host has failed to respond.»
	 */
	return @file_get_contents($url, null, stream_context_create(['http' => df_clean([
		'ignore_errors' => true, 'timeout' => $timeout
	])]));
}

/**
 * 2016-04-13
 * @see df_request_body_json()
 * @used-by \Df\Core\Visitor::responseA()
 * @used-by \Dfe\AmazonLogin\Customer::response()
 * @used-by \Dfe\CurrencyConvert\Ecb::rates()
 * @used-by \Dfe\GoogleBackendLogin\Plugin\Backend\App\AbstractAction::beforeDispatch()
 * @used-by \Dfe\Paymill\T\CaseT::token()
 * @used-by \Dfe\Salesforce\T\Basic::t02_the_latest_version()
 * @param string $urlBase
 * @param array(string => string) $params [optional]
 * @param int|null $timeout [optional]
 * @return array(string => mixed)
 */
function df_http_json($urlBase, array $params = [], $timeout = null) {return
	/** @var string|bool $json */ /** @var bool|array|null $r */
	false === ($json = df_http_get($urlBase, $params, $timeout))
	|| !is_array($r = df_json_decode($json))
	? [] : $r
;}

/**
 * @used-by df_scope()
 * @used-by df_store()   
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
 * @used-by \Dfe\Stripe\Controller\CustomerReturn\Index::isSuccess()
 * @used-by \Doormall\Shipping\Controller\Index\Index::execute()
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
 * @return string|false
 */
function df_request_body() {return file_get_contents('php://input');}

/**
 * 2017-03-09
 * @see df_http_json()
 * @used-by \Df\Payment\W\Reader\Json::http()
 * @return string
 */
function df_request_body_json() {return !($j = df_request_body()) ? [] : df_json_decode($j);}

/**
 * 2016-12-25
 * The @uses \Zend\Http\Request::getHeader() method is insensitive to the argument's letter case:
 * @see \Zend\Http\Headers::createKey()
 * https://github.com/zendframework/zendframework/blob/release-2.4.6/library/Zend/Http/Headers.php#L462-L471
 * @used-by df_request_header()
 * @used-by df_request_ua()
 * @used-by \Dfe\Qiwi\W\Reader::http()
 * @param string $k
 * @return string|false
 */
function df_request_header($k) {return df_request_o()->getHeader($k);}

/**
 * 2015-08-14
 * https://github.com/magento/magento2/issues/1675
 * @used-by df_action_name()
 * @used-by df_is_ajax()
 * @used-by df_request()
 * @used-by df_request_header()
 * @used-by \Df\Framework\Action::module()
 * @used-by \Df\Sentry\Client::get_http_data()
 * @used-by \Dfe\Portal\Block\Content::getTemplate()
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 * @used-by \Dfr\Core\Realtime\Dictionary::translate()
 * @return IRequest|RequestHttp
 */
function df_request_o() {return df_o(IRequest::class);}

/**
 * 2016-12-25
 * 2017-02-18
 * Модуль Checkout.com раньше использовал dfa($_SERVER, 'HTTP_USER_AGENT')
 * @used-by \Dfe\CheckoutCom\Charge::metaData()
 * @used-by \Dfe\Spryng\P\Charge::p()
 * @return string|false
 */
function df_request_ua() {return df_request_header('user-agent');}