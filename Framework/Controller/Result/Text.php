<?php
namespace Df\Framework\Controller\Result;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\AbstractResult;
// 2016-07-04
class Text extends AbstractResult {
	/**
	 * 2016-07-04
	 * @override
	 * @see \Magento\Framework\Controller\AbstractResult::render()
	 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/Controller/AbstractResult.php#L109-L113
	 * @param ResponseInterface|ResponseHttp $response
	 * @return $this
	 */
	protected function render(ResponseInterface $response) {
		$response->setContent($this->_content);
		df_response_content_type('text/plain; charset=utf-8', $response);
		return $this;
	}

	/**
	 * 2016-07-04
	 * @var string
	 */
	private $_content;

	/**
	 * 2016-07-04
	 * @param string $text
	 * @return self
	 */
	public static function i($text) {
		/** @var self $result */
		$result = new self;
		$result->_content = $text;
		return $result;
	}
}