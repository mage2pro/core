<?php
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Authentication;
/**
 * 2016-07-31
 * К сожалению, мы не можем указывать кодировку в обработчике,
 * установленном @see set_exception_handler(),
 * потому что @see set_exception_handler() в Magento работать не будет
 * из-за глобального try..catch в методе @see Mage::run()
 *
 * 2015-01-28
 * По примеру @see df_handle_entry_point_exception()
 * добавил условие @uses Mage::getIsDeveloperMode()
 * потому что Magento выводит диагностические сообщения на экран
 * только при соблюдении этого условия.
 * @return void
 */
function df_header_utf() {
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=UTF-8');
	}
}

/**
 * 2017-02-26      
 * @used-by df_response_sign()
 * @param array(string => string) $a [optional]
 * @return array(string => string)
 */
function df_headers(array $a = []) {return dfa_key_transform($a + [
	'Author' => 'Dmitry Fedyuk'
	,'EMail' => 'admin@mage2.pro'
	,'Website' => 'https://mage2.pro'
], function($k) {return "X-Mage2.PRO-{$k}";});}

/**             
 * 2016-12-04
 * @return Context
 */
function df_http_context() {return df_o(Context::class);}

/**
 * 2016-11-09
 * «How to get credentials from the HTTP basic access authentication?» https://mage2.pro/t/2257
 * Returns [$login, $password] pair.
 * @return string[]
 */
function df_http_credentials() {
	/** @var Authentication $auth */
	$auth = df_o(Authentication::class);
	return $auth->getCredentials();
}

/**
 * 2015-11-27
 * Google API в случае сбоя возвращает корректный JSON, но с кодом HTTP 403,
 * что приводит к тому, что @see file_get_contents() не просто возвращает JSON,
 * а создаёт при этом warning.
 * Чтобы при коде 403 warning не создавался, использую ключ «ignore_errors»:
 * http://php.net/manual/en/context.http.php#context.http.ignore-errors
 * http://stackoverflow.com/a/21976746
 *
 * Обратите внимание, что для использования @uses file_get_contents
 * с адресами https требуется расширение php_openssl интерпретатора PHP,
 * однако оно является системным требованием Magento 2:
 * http://devdocs.magento.com/guides/v2.0/install-gde/system-requirements.html#required-php-extensions
 * Поэтому мы вправе использовать здесь @uses file_get_contents
 *
 * @param $urlBase
 * @param array(string => string) $params [optional]
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
 * @param int|null $timeout [optional]
 *
 *
 * @return string|bool
 * The function returns the read data or FALSE on failure.
 * http://php.net/manual/function.file-get-contents.php
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
	return @file_get_contents($url, null, stream_context_create([
		'http' => df_clean(['ignore_errors' => true, 'timeout' => $timeout])
	]));
}

/**
 * 2016-04-13
 * @param string $urlBase
 * @param array(string => string) $params [optional]
 * @param int|null $timeout [optional]
 * @return array(string => mixed)
 */
function df_http_json($urlBase, array $params = [], $timeout = null) {
	/** @var array|mixed $result */
	$result = [];
	/** @var string|bool $json */
	$json = df_http_get($urlBase, $params, $timeout);
	if (false !== $json) {
		/** @var bool|array|null $decoded */
		$decoded = df_json_decode($json);
		if (is_array($decoded)) {
			$result = $decoded;
		}
	}
	return $result;
}

/**
 * 2016-07-18
 * @param string $urlBase
 * @param array(string => string) $params [optional]
 * @param int|null $timeout [optional]
 * @return array(string => mixed)
 */
function df_http_json_c($urlBase, array $params = [], $timeout = null) {return
	df_cache_get_simple(
		[$urlBase, $params, $timeout]
		/** @uses df_http_json() */
		, 'df_http_json'
		, $urlBase, $params, $timeout
	)
;}

/**
 * @param string|null $key [optional]
 * @param string|null|callable $default [optional]
 * @return string|array(string => string)
 */
function df_request($key = null, $default = null) {
	/** @var string|array(string => string) $result */
	if (is_null($key)) {
		$result = df_request_o()->getParams();
	}
	else {
		$result = df_request_o()->getParam($key);
		$result = df_if1(is_null($result) || '' === $result, $default, $result);
	}
	return $result;
}

/**
 * 2016-12-25
 * The @uses \Zend\Http\Request::getHeader() method is insensitive to the argument's letter case:
 * @see \Zend\Http\Headers::createKey()
 * https://github.com/zendframework/zendframework/blob/release-2.4.6/library/Zend/Http/Headers.php#L462-L471
 * @param string $key
 * @return string|false
 */
function df_request_header($key) {return df_request_o()->getHeader($key);}

/**
 * 2015-08-14
 * https://github.com/magento/magento2/issues/1675
 * @return RequestInterface|RequestHttp
 */
function df_request_o() {return df_o(RequestInterface::class);}

/**
 * 2016-12-25
 * 2017-02-18
 * Модуль Checkout.com раньше использовал dfa($_SERVER, 'HTTP_USER_AGENT')
 * @used-by \Dfe\CheckoutCom\Charge::metaData()
 * @used-by \Dfe\Spryng\Charge::pCharge()
 * @return string|false
 */
function df_request_ua() {return df_request_header('user-agent');}

/**
 * 2015-08-14
 * @return string
 */
function df_ruri() {static $r; return $r ? $r : $r = df_request_o()->getRequestUri();}

/**
 * 2015-08-14
 * @param string $needle
 * @return bool
 */
function df_ruri_contains($needle) {return df_contains(df_ruri(), $needle);}