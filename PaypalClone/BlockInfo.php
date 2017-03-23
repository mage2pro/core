<?php
namespace Df\PaypalClone;
use Df\Payment\W\Event;
/**
 * 2016-08-29
 * @method Method m()
 * @see \Df\GingerPaymentsBase\Block\Info
 * @see \Dfe\AllPay\Block\Info
 * @see \Dfe\SecurePay\Block\Info
 */
abstract class BlockInfo extends \Df\Payment\Block\Info {
	/**
	 * 2016-11-17
	 * @override
	 * @see \Df\Payment\Block\Info::isWait()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 * @return bool
	 */
	final protected function isWait() {return parent::isWait() || !$this->responseF();}

	/**
	 * 2016-07-18     
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	protected function responseF($k = null) {return df_tm($this->m())->responseF($k);}
}