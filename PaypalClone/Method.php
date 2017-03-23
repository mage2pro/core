<?php
namespace Df\PaypalClone;
use Df\Payment\W\Event;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-08-27
 * @see \Df\GingerPaymentsBase\Method
 * @see \Df\PaypalClone\Method\Normal
 * @see \Dfe\Klarna\Method
 */
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-07-18  
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\PaypalClone\BlockInfo::responseF()
	 * @used-by \Dfe\AllPay\Method::getInfoBlockType()
	 * @used-by \Dfe\AllPay\Method::paymentOptionTitle()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	function responseF($k = null) {return $this->tm()->responseF($k);}

	/**
	 * 2017-03-05
	 * @used-by responseF()
	 * @used-by \Df\PaypalClone\BlockInfo::responseL()
	 * @used-by \Df\PaypalClone\Refund::tm()
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @return TM
	 */
	final function tm() {return dfc($this, function() {return new TM($this);});}
}