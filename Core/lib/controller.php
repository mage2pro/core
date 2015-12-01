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
 * 2015-11-28
 * @param mixed $data
 * @return \Magento\Framework\Controller\Result\Json
 */
function df_controller_json($data) {
	/** @var \Magento\Framework\Controller\Result\Json $result */
	$result = df_o('Magento\Framework\Controller\Result\Json');
	return $result->setData($data);
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

