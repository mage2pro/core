<?php
namespace Df\Framework\Controller\Result;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json as _Json;
class Json extends _Json {
	/**
	 * 2016-03-18
	 * @override
	 * @see \Magento\Framework\App\Response\Http::representJson()
	 * «The @see \Magento\Framework\App\Response\Http::representJson()
	 * does not specifies a JSON response's charset and removes a previously specified charset,
	 * so not-latin characters are rendered incorrectly by all the modern browsers»
	 * https://mage2.pro/t/976
	 * @param ResponseInterface $response
	 * @return $this
	 */
	protected function render(ResponseInterface $response) {
		parent::render($response);
		df_response_content_type('application/json; charset=utf-8', $response);
		return $this;
	}

	/**
	 * 2016-07-04
	 * @param string $data
	 * @return self
	 */
	public static function i($data) {
		return df_create(__CLASS__)->setJsonData(is_array($data) ? df_json_encode($data) : $data);
	}
}


