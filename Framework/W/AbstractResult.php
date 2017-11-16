<?php
namespace Df\Framework\W;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
/**
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
 */
abstract class AbstractResult implements ResultInterface
{
    /**
     * @var int
     */
    protected $httpResponseCode;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $statusHeaderCode;

    /**
     * @var string
     */
    protected $statusHeaderVersion;

    /**
     * @var string
     */
    protected $statusHeaderPhrase;

    /**
     * Set response code to result
     *
     * @param int $httpCode
     * @return $this
     */
    function setHttpResponseCode($httpCode)
    {
        $this->httpResponseCode = $httpCode;
        return $this;
    }

    /**
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param string $name
     * @param string $value
     * @param boolean $replace
     * @return $this
     */
    function setHeader($name, $value, $replace = false)
    {
        $this->headers[] = [
            'name'    => $name,
            'value'   => $value,
            'replace' => $replace,
        ];
        return $this;
    }

    /**
     * @param int|string $httpCode
     * @param null|int|string $version
     * @param null|string $phrase
     * @return $this
     */
    function setStatusHeader($httpCode, $version = null, $phrase = null)
    {
        $this->statusHeaderCode = $httpCode;
        $this->statusHeaderVersion = $version;
        $this->statusHeaderPhrase = $phrase;
        return $this;
    }

    /**
     * @param HttpResponseInterface $response
     * @return $this
     */
    protected function applyHttpHeaders(HttpResponseInterface $response)
    {
        if (!empty($this->httpResponseCode)) {
            $response->setHttpResponseCode($this->httpResponseCode);
        }
        if ($this->statusHeaderCode) {
            $response->setStatusHeader(
                $this->statusHeaderCode,
                $this->statusHeaderVersion,
                $this->statusHeaderPhrase
            );
        }
        if (!empty($this->headers)) {
            foreach ($this->headers as $headerData) {
                $response->setHeader($headerData['name'], $headerData['value'], $headerData['replace']);
            }
        }
        return $this;
    }
    
    /**
     * @param HttpResponseInterface $response
     * @return $this
     */
    abstract protected function render(HttpResponseInterface $response);

    /**
     * Render content
     *
     * @param HttpResponseInterface|ResponseInterface $response
     * @return $this
     */
    function renderResult(ResponseInterface $response)
    {
        $this->applyHttpHeaders($response);
        return $this->render($response);
    }
}
