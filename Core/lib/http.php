<?php
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
function df_http_json_c($urlBase, array $params = [], $timeout = null) {
	return df_cache_get_simple(
		md5(implode([$urlBase, http_build_query($params)]))
		/** @uses df_http_json() */
		, 'df_http_json'
		, $urlBase, $params, $timeout
	);
}