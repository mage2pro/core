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
 * @see \Df\Framework\W\Result\Text
 * @see \Dfe\Qiwi\Result
 * @see \Dfe\YandexKassa\Result
 */
abstract class Result implements ResultInterface {
	/**
	 * 2017-03-30
	 * @used-by \Df\Payment\W\Action::execute()
	 * @see \Df\Framework\W\Result\Text::__toString()
	 * @see \Dfe\Qiwi\Result::__toString()
	 * @return string
	 */
	abstract function __toString();

	/**
	 * 2016-08-24
	 * @see \Magento\Framework\Controller\AbstractResult::render()
	 * @used-by renderResult()
	 * @see \Df\Framework\W\Result\Text::render()
	 * @see \Dfe\Qiwi\Result::render()
	 * @see \Dfe\YandexKassa\Result::render()
	 * @param IHttpResponse|HttpResponse $r
	 */
	abstract protected function render(IHttpResponse $r);

	/**
	 * 2016-08-24 Render content.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Controller\ResultInterface::renderResult()
	 * @see \Magento\Framework\Controller\AbstractResult::renderResult()
	 * @used-by \Magento\Framework\App\Http::launch():
	 *		// TODO: Temporary solution until all controllers return ResultInterface (MAGETWO-28359)
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
		$this->applyHttpHeaders($r);
		$this->render($r);
		return null;
	}

	/**
	 * 2016-08-24 Set a header. If $replace is true, replaces any headers already defined with that $name.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Controller\ResultInterface::setHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::setHeader()
	 * @param string $name
	 * @param string $value
	 * @param boolean $replace
	 * @return $this
	 */
	function setHeader($name, $value, $replace = false) {
		$this->_headers[] = ['name' => $name, 'replace' => $replace, 'value' => $value];
		return $this;
	}

	/**
	 * 2016-08-24 Set response code to result
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Controller\ResultInterface::setHttpResponseCode()
	 * @see \Magento\Framework\Controller\AbstractResult::setHttpResponseCode()
	 * @param int $v
	 * @return $this
	 */
	function setHttpResponseCode($v) {$this->_httpResponseCode = $v; return $this;}

	/**
	 * 2016-08-24
	 * 2017-11-17 @todo Currently it is not used.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @see \Magento\Framework\Controller\AbstractResult::setStatusHeader()
	 * @used-by applyHttpHeaders()
	 * @param int|string $httpCode
	 * @param null|int|string $version
	 * @param null|string $phrase
	 * @return $this
	 */
	function setStatusHeader($httpCode, $version = null, $phrase = null) {
		$this->_statusHeaderCode = $httpCode;
		$this->_statusHeaderVersion = $version;
		$this->_statusHeaderPhrase = $phrase;
		return $this;
	}

	/**
	 * 2016-08-24
	 * @used-by renderResult()
	 * @see \Magento\Framework\Controller\AbstractResult::applyHttpHeaders()
	 * @param IHttpResponse|HttpResponse $r
	 */
	private function applyHttpHeaders(IHttpResponse $r) {
		if ($this->_httpResponseCode) {
			$r->setHttpResponseCode($this->_httpResponseCode);
		}
		if ($this->_statusHeaderCode) {
			$r->setStatusHeader(
				$this->_statusHeaderCode, $this->_statusHeaderVersion, $this->_statusHeaderPhrase
			);
		}
		foreach ($this->_headers as $headerData) {
			$r->setHeader($headerData['name'], $headerData['value'], $headerData['replace']);
		}
	}

	/**
	 * 2016-08-24
	 * @used-by applyHttpHeaders()
	 * @used-by setHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::$headers
	 * @var array
	 */
	private $_headers = [];

	/**
	 * 2016-08-24
	 * @used-by applyHttpHeaders()
	 * @used-by setStatusHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::$httpResponseCode
	 * @var int
	 */
	private $_httpResponseCode;

	/**
	 * 2016-08-24
	 * @used-by applyHttpHeaders()
	 * @used-by setStatusHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::$statusHeaderCode
	 * @var string
	 */
	private $_statusHeaderCode;

	/**
	 * 2016-08-24
	 * @used-by applyHttpHeaders()
	 * @used-by setStatusHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::$statusHeaderPhrase
	 * @var string
	 */
	private $_statusHeaderPhrase;

	/**
	 * 2016-08-24
	 * @used-by applyHttpHeaders()
	 * @used-by setStatusHeader()
	 * @see \Magento\Framework\Controller\AbstractResult::$statusHeaderVersion
	 * @var string
	 */
	private $_statusHeaderVersion;
}