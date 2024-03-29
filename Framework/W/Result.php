<?php
namespace Df\Framework\W;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\HttpInterface as IHttpResponse;
use Magento\Framework\App\ResponseInterface as IResponse;
use Magento\Framework\Controller\ResultInterface;
/**
 * 2016-08-24
 * https://github.com/mage2pro/core/blob/3.3.19/Framework/W/Result/Text.php#L7-L27
 * 2017-11-19
 * Note 1.
 * Magento 2 team has changes some @see \Magento\Framework\Controller\AbstractResult methods signatures
 * in Magento >= 2.2 by the following commit: https://github.com/magento/magento2/commit/c9309328
 * The methods are:
 * 1) @see \Magento\Framework\Controller\AbstractResult::render()
 * https://github.com/magento/magento2/blob/2.1.9/lib/internal/Magento/Framework/Controller/AbstractResult.php#L109-L113
 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/Controller/AbstractResult.php#L110-L114
 * 2) @see \Magento\Framework\Controller\AbstractResult::applyHttpHeaders()
 * https://github.com/magento/magento2/blob/2.1.9/lib/internal/Magento/Framework/Controller/AbstractResult.php#L85-L89
 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/Controller/AbstractResult.php#L86-L90
 * As these methods have different signatures in different Magento versions,
 * it is impossible to override them in a descendant class in a reusable way.
 * So I just do not use the @see \Magento\Framework\Controller\AbstractResult class at all,
 * and have copied it in its Magento 2.2 versions as this class.
 *
 * Note 2.
 * I have removed setStatusHeader().
 * If I will want to restore it, then I can find it here:
 * https://github.com/mage2pro/core/blob/3.3.20/Framework/W/Result.php#L103-L119
 *
 * @see \Df\Framework\W\Result\Text
 * @see \Df\Framework\W\Result\Xml
 * @see \Justuno\M2\W\Result\Js
 * @see \TFC\GoogleShopping\Result (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
 */
abstract class Result implements ResultInterface {
	/**
	 * 2017-03-30
	 * @used-by \Df\Payment\W\Action::execute()
	 * @see \Df\Framework\W\Result\Text::__toString()
	 * @see \Df\Framework\W\Result\Xml::__toString()
	 * @see \Justuno\M2\W\Result\Js::__toString()
	 * @see \TFC\GoogleShopping\Result::__toString() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
	 */
	abstract function __toString():string;

	/**
	 * 2016-08-24
	 * @used-by self::renderResult()
	 * @see \Df\Framework\W\Result\Text::render()
	 * @see \Df\Framework\W\Result\Xml::render()
	 * @see \Justuno\M2\W\Result\Js::render()
	 * ---
	 * @see \Magento\Framework\Controller\AbstractResult::render()
	 * @param IHttpResponse|HttpResponse $r
	 */
	abstract protected function render(IHttpResponse $r):void;

	/**
	 * 2016-08-24 Render content.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Controller\ResultInterface::renderResult()
	 * @see \Magento\Framework\Controller\AbstractResult::renderResult()
	 * @used-by \Magento\Framework\App\Http::launch():
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
	 * @param IResponse|IHttpResponse|HttpResponse $r
	 * @return null It is not used.
	 */
	function renderResult(IResponse $r) {
		if ($this->_code) {
			$r->setHttpResponseCode($this->_code);
		}
		foreach ($this->_headers as $headerData) {
			$r->setHeader($headerData['name'], $headerData['value'], $headerData['replace']);
		}
		$this->render($r);
		return null;
	}

	/**
	 * 2016-08-24 Set a header. If $replace is true, replaces any headers already defined with that $name.
	 * 2022-12-01 We can not declare arguments types because they are undeclared in the overriden method.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Controller\ResultInterface::setHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::setHeader()
	 * @param string $name
	 * @param string $value
	 * @param bool $replace
	 */
	function setHeader($name, $value, $replace = false):self {
		$this->_headers[] = ['name' => $name, 'replace' => $replace, 'value' => $value]; return $this;
	}

	/**
	 * 2016-08-24 Set response code to result
	 * 2022-12-01 We can not declare arguments types because they are undeclared in the overriden method.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Controller\ResultInterface::setHttpResponseCode()
	 * @see \Magento\Framework\Controller\AbstractResult::setHttpResponseCode()
	 * @param int $v
	 */
	function setHttpResponseCode($v):self {$this->_code = $v; return $this;}

	/**
	 * 2016-08-24
	 * @used-by self::renderResult()
	 * @used-by self::setHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::$headers
	 * @var array
	 */
	private $_headers = [];

	/**
	 * 2016-08-24
	 * @used-by self::renderResult()
	 * @used-by self::setStatusHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::$httpResponseCode
	 * @var int
	 */
	private $_code;
}