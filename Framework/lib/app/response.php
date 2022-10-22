<?php
use Df\Framework\W\Result as wResult;
use Magento\Framework\App\ResponseInterface as IResponse;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\HttpInterface as IHttpResponse;
use Magento\Framework\Controller\ResultInterface as IResult;
use Magento\Framework\Webapi\Rest\Response as RestResponse;

/**
 * 2017-02-01
 * Добавил параметр $r.
 * IResult и wResult не родственны IResponse и HttpResponse.
 * 2017-11-17
 * You can read here more about the IResult/wResult and IResponse/HttpResponse difference:
 * 1) @see \Magento\Framework\App\Http::launch():
 *		# TODO: Temporary solution until all controllers return ResultInterface (MAGETWO-28359)
 *		if ($result instanceof ResultInterface) {
 *			$this->registry->register('use_page_cache_plugin', true, true);
 *			$result->renderResult($this->_response);
 *		} elseif ($result instanceof HttpInterface) {
 *			$this->_response = $result;
 *		} else {
 *			throw new \InvalidArgumentException('Invalid return type');
 *		}
 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Http.php#L122-L149
 * 2) "[Question] To ResultInterface or not ResultInterface": https://github.com/magento/magento2/issues/1355
 * https://github.com/magento/magento2/issues/1355
 * @used-by df_is_redirect()
 * @used-by df_redirect()
 * @used-by df_redirect_back()
 * @used-by df_response_ar()
 * @used-by df_response_code()
 * @used-by df_response_content_type()
 * @used-by df_router_redirect()
 * @used-by \Df\Framework\App\Action\Image::execute()
 * @param IResult|wResult|IResponse|HttpResponse|null $r [optional]
 * @return IResponse|IHttpResponse|HttpResponse|IResult|wResult
 */
function df_response($r = null) {return $r ?: df_o(
	/**
	 * 2021-09-22
	 * 1) @uses \Magento\Framework\Webapi\Rest\Response implements
	 * @uses \Magento\Framework\App\ResponseInterface:
	 * 1.1)
	 * 		namespace Magento\Framework\Webapi\Rest;
	 * 		class Response extends \Magento\Framework\Webapi\Response
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Webapi/Rest/Response.php#L8-L10
	 * https://github.com/magento/magento2/blob/2.4.3/lib/internal/Magento/Framework/Webapi/Rest/Response.php#L8-L10
	 * 1.2)
	 * 		namespace Magento\Framework\Webapi;
	 * 		class Response
	 * 			extends \Magento\Framework\HTTP\PhpEnvironment\Response
	 * 			implements \Magento\Framework\App\Response\HttpInterface
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Webapi/Response.php#L8-L11
	 * https://github.com/magento/magento2/blob/2.4.3/lib/internal/Magento/Framework/Webapi/Response.php#L8-L11
	 * 1.3)
	 * 		namespace Magento\Framework\App\Response;
	 *		interface HttpInterface extends \Magento\Framework\App\ResponseInterface
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Response/HttpInterface.php#L8-L10
	 * https://github.com/magento/magento2/blob/2.4.3/lib/internal/Magento/Framework/App/Response/HttpInterface.php#L6-L14
	 * 2) But @uses \Magento\Framework\Webapi\Rest\Response is not instantiated via the
	 * @uses \Magento\Framework\App\ResponseInterface
	 * It is instantiated directly via the  `Magento\Framework\Webapi\Rest\Response` class name:
	 *		public function __construct(
	 *			RestResponse $response,
	 *			<…>
	 *		) {
	 *			$this->response = $response;
	 *			<…>
	 *		}
	 * https://github.com/magento/magento2/blob/2.4.3/app/code/Magento/Webapi/Controller/Rest/SynchronousRequestProcessor.php#L54-L78
	 */
	df_is_rest() ? RestResponse::class : IResponse::class
);}

/**
 * 2017-02-01
 * @used-by df_response_headers()
 * @used-by df_response_sign()
 * @param IResult|wResult|IHttpResponse|HttpResponse|null|array(string => string) $a1 [optional]
 * @param IResult|wResult|IHttpResponse|HttpResponse|null|array(string => string) $a2 [optional]
 * @return array(array(string => string), IResult|wResult|IHttpResponse|HttpResponse)
 */
function df_response_ar($a1 = null, $a2 = null) {return
	is_array($a1) ? [$a1, df_response($a2)] : (
		is_array($a2) ? [$a2, df_response($a1)] : (
			is_object($a1) ? [[], $a1] : (
				is_object($a2) ? [[], $a2] :
					[[], df_response()]
			)
		)
	)
;}

/**
 * 2015-12-09
 * @used-by \Df\Framework\App\Action\Image::execute()
 * @used-by \Df\GoogleFont\Controller\Index\Index::execute()
 */
function df_response_cache_max() {df_response_headers([
	'Cache-Control' => 'max-age=315360000'
	,'Expires' => 'Thu, 31 Dec 2037 23:55:55 GMT'
	# 2015-12-09
	# Если не указывать заголовок Pragma, то будет добавлено Pragma: no-cache.
	# Так и не разобрался, кто его добавляет. Может, PHP или веб-сервер.
	# Простое df_response()->clearHeader('pragma') не позволяет от него избавиться.
	# http://stackoverflow.com/questions/11992946
	,'Pragma' => 'cache'
]);}

/**
 * I pass the 3rd argument ($replace = true) to @uses \Magento\Framework\HTTP\PhpEnvironment\Response::setHeader()
 * because the `Content-Type` headed can be already set.
 * @used-by \Df\Framework\App\Action\Image::execute()
 * @used-by \Df\Framework\W\Result\Text::render()
 * @used-by \Df\Framework\W\Result\Xml::render()
 * @param string $contentType
 * @param IResult|wResult|IHttpResponse|HttpResponse|null $r [optional]
 */
function df_response_content_type($contentType, $r = null) {df_response($r)->setHeader('Content-Type', $contentType, true);}

/**
 * 2015-11-29
 * 2017-02-01
 * @used-by df_response_cache_max()
 * @used-by df_response_sign()
 * @used-by \Df\Framework\App\Action\Image::execute()
 * @param IResult|wResult|IHttpResponse|HttpResponse|null|array(string => string) $a1 [optional]
 * @param IResult|wResult|IHttpResponse|HttpResponse|null|array(string => string) $a2 [optional]
 * @return IResult|wResult|IHttpResponse|HttpResponse
 */
function df_response_headers($a1 = null, $a2 = null) {
	/** @var array(string => string) $a */ /** @var IResult|wResult|IHttpResponse|HttpResponse $r */
	# 2020-03-02
	# The square bracket syntax for array destructuring assignment (`[…] = […]`) requires PHP ≥ 7.1:
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	list($a, $r) = df_response_ar($a1, $a2);
	array_walk($a, function($v, $k) use($r) {$r->setHeader($k, $v, true);});
	return $r;
}

/**
 * 2017-02-01
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @used-by \Df\Payment\W\Action::execute()
 * @param IResult|wResult|IHttpResponse|HttpResponse|null|array(string => string) $a1 [optional]
 * @param IResult|wResult|IHttpResponse|HttpResponse|null|array(string => string) $a2 [optional]
 * @return IResult|wResult|IHttpResponse|HttpResponse
 */
function df_response_sign($a1 = null, $a2 = null) {
	/** @var array(string => string) $a */ /** @var IResult|wResult|IHttpResponse|HttpResponse $r */
	# 2020-03-02
	# The square bracket syntax for array destructuring assignment (`[…] = […]`) requires PHP ≥ 7.1:
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	list($a, $r) = df_response_ar($a1, $a2);
	return df_response_headers($r, df_headers($a));
}