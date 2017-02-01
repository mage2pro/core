<?php
use Magento\Framework\Controller\ResultInterface as IResult;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\App\ResponseInterface as IResponse;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\HTTP\PhpEnvironment\Response as ResponsePhp;
use Df\Framework\Controller\AbstractResult as DfResult;
/**
 * 2015-11-29
 * @param string $contents
 * @return Raw
 */
function df_controller_raw($contents) {
	/** @var Raw $result */
	$result = df_create(Raw::class);
	return $result->setContents($contents);
}

/** @return IResponse|ResponseHttp */
function df_response() {return df_o(IResponse::class);}

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
 * @param IResponse|null $response [optional]
 * @return void
 */
function df_response_content_type($contentType, IResponse $response = null) {
	if (!$response) {
		$response = df_response();
	}
	$response->setHeader('Content-Type', $contentType, $replace = true);
}

/**
 * 2015-11-29
 * 2017-02-01
 * 1) @uses \Df\Framework\Controller\AbstractResult::setHeader()
 * 2) @uses \Magento\Framework\Controller\ResultInterface::setHeader()
 * 3) @uses \Magento\Framework\HTTP\PhpEnvironment\Response::setHeader()
 * №1 и №2 не родственны №3.
 * @param array(string => string) $headers
 * @param IResult|DfResult|null $r [optional]
 * @return void
 */
function df_response_headers(array $headers, $r = null) {
	$r = $r ?: df_response();
	array_walk($headers, function($v, $k) use($r) {$r->setHeader($k, $v, true);});
}

/**
 * 2015-12-09
 * @return void
 */
function df_response_cache_max() {df_response_headers([
	'Cache-Control' => 'max-age=315360000'
	,'Expires' => 'Thu, 31 Dec 2037 23:55:55 GMT'
	// 2015-12-09
	// Если не указывать заголовок Pragma, то будет добавлено Pragma: no-cache.
	// Так и не разобрался, кто его добавляет. Может, PHP или веб-сервер.
	// Простое df_response()->clearHeader('pragma') не позволяет от него избавиться.
	// http://stackoverflow.com/questions/11992946
	,'Pragma' => 'cache'
]);}