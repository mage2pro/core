<?php
namespace Df\Framework\W\Result;
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
 * Fatal error: Declaration of Df\Framework\W\Result\Json::render()
 * must be compatible with Magento\Framework\Controller\AbstractResult::render
 * (Magento\Framework\App\Response\HttpInterface $response)
 * in C:\work\mage2.pro\store\vendor\mage2pro\core\Framework\Controller\Result\Json.php on line 5
 *
 * Поэтому вместо наследования от @see \Magento\Framework\Controller\Result\Json
 * просто копируем его реализацию в класс @see \Df\Framework\W\Result\JsonM.
 *
 * @see \Df\Framework\W\Result\Json
 */
class Text extends \Df\Framework\W\Result {
	/**
	 * 2017-03-30
	 * @override
	 * @see \Df\Framework\W\Result::__toString()
	 * @used-by \Df\Payment\W\Action::execute()
	 * @return string
	 */
	final function __toString() {return $this->_body;}

	/**
	 * 2016-08-24
	 * @used-by render()
	 * @see \Df\Framework\W\Result\Json::contentType()
	 * @see \Dfe\Qiwi\Result::contentType()
	 * @return string
	 */
	protected function contentType() {return 'text/plain';}

	/**
	 * 2016-08-24
	 * @used-by \Df\Framework\W\Result\Text::i()
	 * @see \Df\Framework\W\Result\Json::prepare()
	 * @param mixed $body
	 * @return string
	 */
	protected function prepare($body) {return $body;}

	/**
	 * 2016-07-04
	 * @override
	 * @see \Magento\Framework\Controller\AbstractResult::render()
	 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/Controller/AbstractResult.php#L109-L113
	 * @param IHttp|Http $res
	 * @return $this
	 */
	protected function render(IHttp $res) {
		$res->setBody($this->_body);
		df_response_content_type(implode('; ', [$this->contentType(), 'charset=utf-8']), $res);
		return $this;
	}

	/**
	 * 2016-07-04
	 * @var string
	 */
	private $_body;

	/**
	 * 2016-07-04
	 * @used-by \Dfe\AllPay\W\Responder::error()
	 * @used-by \Dfe\AllPay\W\Responder::success()
	 * @used-by \Df\Payment\W\Responder::setIgnored()
	 * @used-by \Df\Payment\W\Responder::setSoftFailure()
	 * @used-by \Df\Payment\W\Responder::notForUs()
	 * @used-by \Df\Payment\W\Responder::success()
	 * @used-by \Df\Payment\W\Responder::defaultError()
	 * @used-by \Dfe\Dragonpay\W\Responder::success()
	 * @used-by \Dfe\IPay88\W\Responder::success()
	 * @used-by \Dfe\Robokassa\W\Responder::success()
	 * @param mixed $body
	 * @return self
	 */
	static function i($body) {
		$result = new static; /** @var self $result */
		$result->_body = $result->prepare($body);
		return $result;
	}
}