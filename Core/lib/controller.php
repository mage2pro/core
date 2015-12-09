<?php
/** @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\App\Response\Http */
function df_response() {return df_o('Magento\Framework\App\ResponseInterface');}

/**
 * 2015-11-29
 * @param int $value
 * @return void
 */
function df_response_code($value) {df_response()->setHttpResponseCode($value);}

/**
 * При установке заголовка HTTP «Content-Type»
 * надёжнее всегда добавлять 3-й параметр: $replace = true,
 * потому что заголовок «Content-Type» уже ранее был установлен методом
 * @param string $contentType
 * @return void
 */
function df_response_content_type($contentType) {
	df_response()->setHeader('Content-Type', $contentType, $replace = true);
}

/**
 * 2015-11-29
 * @param array(string => string) $headers
 * @return void
 */
function df_response_headers(array $headers) {
	array_walk($headers, function($value, $key) {
		df_response()->setHeader($key, $value, true);
	});
}

/**
 * 2015-12-09
 * @return void
 */
function df_response_cache_max() {
	df_response_headers([
		'Cache-Control' => 'max-age=315360000'
		,'Expires' => 'Thu, 31 Dec 2037 23:55:55 GMT'
		/**
		 * Если не указывать заголовок Pragma, то будет добавлено Pragma: no-cache.
		 * Так и не разобрался, кто его добавляет. Может, PHP или веб-сервер.
		 * Простое df_response()->clearHeader('pragma');
		 * не позволяет от него избавиться.
		 * http://stackoverflow.com/questions/11992946
		 */
		,'Pragma' => 'cache'
	]);
}

/**
 * 2015-11-28
 * @param mixed $data
 * @return \Magento\Framework\Controller\Result\Json
 */
function df_controller_json($data) {
	/** @var \Magento\Framework\Controller\Result\Json $result */
	$result = df_o('Magento\Framework\Controller\Result\Json');
	return $result->setJsonData(is_array($data) ? df_json_encode($data) : $data);
}

/**
 * 2015-11-29
 * @param string $contents
 * @return \Magento\Framework\Controller\Result\Raw
 */
function df_controller_raw($contents) {
	/** @var \Magento\Framework\Controller\Result\Raw $result */
	$result = df_o('Magento\Framework\Controller\Result\Raw');
	return $result->setContents($contents);
}

