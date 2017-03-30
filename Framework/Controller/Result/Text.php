<?php
namespace Df\Framework\Controller\Result;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface as IHttp;
/**
 * 2016-07-04
 * 2016-08-24
 * Дибилоид Vinai Kopp 2016-05-12 внёс такой коммит: https://github.com/magento/magento2/commit/c930932
 * Его включили в 2.2-dev 2016-08-10: https://github.com/magento/magento2/blob/c930932/lib/internal/Magento/Framework/Controller/Result/Json.php#L64
 * Этот коммит ломает совместимость сигнатуры метода @see \Magento\Framework\Controller\Result\Json::render()
 * с более ранними версиями.
 *
 * Раньше было:
 * protected function render(ResponseInterface $response)
 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/Controller/Result/Json.php#L64
 *
 * Теперь стало:
 * protected function render(HttpResponseInterface $response)
 *
 * Поэтому мы не можем универсально перекрыть метод render (чтобы это работало сразу во всех версиях):
 * Fatal error: Declaration of Df\Framework\Controller\Result\Json::render()
 * must be compatible with Magento\Framework\Controller\AbstractResult::render
 * (Magento\Framework\App\Response\HttpInterface $response)
 * in C:\work\mage2.pro\store\vendor\mage2pro\core\Framework\Controller\Result\Json.php on line 5
 *
 * Поэтому вместо наследования от @see \Magento\Framework\Controller\Result\Json просто копируем его реализацию
 * в класс @see \Df\Framework\Controller\Result\JsonM.
 */
class Text extends \Df\Framework\Controller\AbstractResult {
	/**
	 * 2017-03-30
	 * @used-by \Df\Payment\W\Action::execute()
	 * @return string
	 */
	final function __toString() {return $this->_body;}

	/**
	 * 2016-08-24
	 * @used-by \Df\Framework\Controller\Result\Text::render()
	 * @return string
	 */
	protected function contentType() {return 'text/plain';}

	/**
	 * 2016-08-24
	 * @used-by \Df\Framework\Controller\Result\Text::i()
	 * @param mixed $body
	 * @return string
	 */
	protected function prepare($body) {return $body;}

	/**
	 * 2016-07-04
	 * @override
	 * @see \Magento\Framework\Controller\AbstractResult::render()
	 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/Controller/AbstractResult.php#L109-L113
	 * @param IHttp|Http $response
	 * @return $this
	 */
	protected function render(IHttp $response) {
		$response->setBody($this->_body);
		df_response_content_type(implode('; ', [$this->contentType(), 'charset=utf-8']), $response);
		return $this;
	}

	/**
	 * 2016-07-04
	 * @var string
	 */
	private $_body;

	/**
	 * 2016-07-04
	 * @param mixed $body
	 * @return self
	 */
	static function i($body) {
		/** @var self $result */
		$result = new static;
		$result->_body = $result->prepare($body);
		return $result;
	}
}