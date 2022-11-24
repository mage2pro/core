<?php
namespace Df\Framework\W\Result;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\HttpInterface as IHttpResponse;
/**
 * 2021-12-03
 * @see \Dfe\Qiwi\Result
 * @see \Dfe\YandexKassa\Result
 * @see \TFC\GoogleShopping\Result
 */
abstract class Xml extends \Df\Framework\W\Result {
	/**
	 * 2021-12-03
	 * @used-by self::__toString()
	 * @see \Dfe\Qiwi\Result::tag()
	 * @see \Dfe\YandexKassa\Result::tag()
	 * @see \TFC\GoogleShopping\Result::tag()
	 */
	abstract protected function tag():string;

	/**
	 * 2021-12-03
	 * We can use the PHP «final» keyword here,
	 * because the method is absent in @see \Magento\Framework\Controller\ResultInterface
	 * @override
	 * @see \Df\Framework\W\Result::__toString()
	 * @used-by self::render()
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	final function __toString():string {return df_xml_g($this->tag(), $this->contents(), $this->attributes());}

	/**
	 * 2021-12-03
	 * @used-by self::__toString()
	 * @see \Dfe\YandexKassa\Result::attributes()
	 * @see \TFC\GoogleShopping\Result::attributes()
	 * @return array(string => mixed)
	 */
	protected function attributes():array {return [];}

	/**
	 * 2021-12-03
	 * @used-by self::__toString()
	 * @see \Dfe\Qiwi\Result::contents()
	 * @see \TFC\GoogleShopping\Result::contents()
	 * @return array(string => mixed)
	 */
	protected function contents():array {return [];}

	/**
	 * 2017-10-02, 2021-12-03
	 * In English: «MIME type: application/xml».
	 * https://tech.yandex.com/money/doc/payment-solution/payment-notifications/payment-notifications-http-docpage
	 * In Russian: «MIME-тип: application/xml».
	 * https://tech.yandex.ru/money/doc/payment-solution/payment-notifications/payment-notifications-http-docpage
	 * @used-by self::render()
	 * @see \Dfe\Qiwi\Result::contentType()
	 */
	protected function contentType():string {return 'application/xml';}

	/**
	 * 2021-12-03
	 * @override
	 * @see \Df\Framework\W\Result::render()
	 * @used-by \Df\Framework\W\Result::renderResult()
	 * @param IHttpResponse|HttpResponse $r
	 */
	final protected function render(IHttpResponse $r):void {
		$r->setBody($this->__toString());
		df_response_content_type($this->contentType(), $r);
	}
}