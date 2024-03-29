<?php
namespace Df\Framework\W\Result;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\HttpInterface as IHttpResponse;
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
 * @used-by \Df\Payment\W\Responder::defaultError()
 * @used-by \Df\Payment\W\Responder::notForUs()
 * @used-by \Df\Payment\W\Responder::setIgnored()
 * @used-by \Df\Payment\W\Responder::setSoftFailure()
 * @used-by \Df\Payment\W\Responder::success()
 * @used-by \Dfe\AllPay\W\Responder::error()
 * @used-by \Dfe\AllPay\W\Responder::success()
 * @used-by \Dfe\Dragonpay\W\Responder::success()
 * @used-by \Dfe\IPay88\W\Responder::success()
 * @used-by \Dfe\Robokassa\W\Responder::success()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 */
class Text extends \Df\Framework\W\Result {
	/**
	 * 2017-03-30
	 * 2017-11-17
	 * We can use the PHP «final» keyword here,
	 * because the method is absent in @see \Magento\Framework\Controller\ResultInterface
	 * @override
	 * @see \Df\Framework\W\Result::__toString()
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	final function __toString():string {return $this->_body;}

	/**
	 * 2016-08-24
	 * @used-by self::render()
	 * @see \Df\Framework\W\Result\Json::contentType()
	 */
	protected function contentType():string {return 'text/plain';}

	/**
	 * 2016-08-24
	 * @used-by self::i()
	 * @see \Df\Framework\W\Result\Json::prepare()
	 * @param mixed $s
	 */
	protected function prepare($s):string {return $s;}

	/**
	 * 2016-07-04
	 * @override
	 * @see \Df\Framework\W\Result::render()
	 * @used-by \Df\Framework\W\Result::renderResult()
	 * @param IHttpResponse|HttpResponse $r
	 */
	final protected function render(IHttpResponse $r):void {
		$r->setBody($this->_body);
		df_response_content_type(implode('; ', [$this->contentType(), 'charset=utf-8']), $r);
	}

	/**
	 * 2016-07-04
	 * @used-by self::__toString()
	 * @used-by self::i()
	 * @used-by self::render()
	 * @var string
	 */
	private $_body;

	/**
	 * 2016-07-04
	 * @used-by \Df\Payment\W\Responder::defaultError()
	 * @used-by \Df\Payment\W\Responder::notForUs()
	 * @used-by \Df\Payment\W\Responder::setIgnored()
	 * @used-by \Df\Payment\W\Responder::setSoftFailure()
	 * @used-by \Df\Payment\W\Responder::success()
	 * @used-by \Dfe\AllPay\W\Responder::error()
	 * @used-by \Dfe\AllPay\W\Responder::success()
	 * @used-by \Dfe\Dragonpay\W\Responder::success()
	 * @used-by \Dfe\IPay88\W\Responder::success()
	 * @used-by \Dfe\Robokassa\W\Responder::success()
	 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
	 * @used-by \Doormall\Shipping\Controller\Index\Index::execute()
	 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
	 * @used-by \Wolf\Filter\Controller\Garage\Clean::execute()
	 * @used-by \Wolf\Filter\Controller\Garage\Index::execute()
	 * @used-by \Wolf\Filter\Controller\Garage\Remove::execute()
	 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
	 * @param mixed $body
	 */
	static function i($body):self {
		$r = new static; /** @var self $r */
		$r->_body = $r->prepare($body);
		return $r;
	}
}