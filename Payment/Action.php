<?php
// 2016-12-25
namespace Df\Payment;
use Df\Payment\Settings as S;
/**
 * 2017-01-07
 * В настоящее время у этого класса 2 наследника:
 * @see \Df\Payment\Action\CustomerReturn
 * @see \Df\Payment\Action\Webhook
 */
abstract class Action extends \Magento\Framework\App\Action\Action {
	/**
	 * 2016-12-25
	 * @used-by \Df\Payment\Action\CustomerReturn::execute()
	 * @return bool
	 */
	protected function needLog() {return $this->s()->log();}

	/**
	 * 2016-12-25
	 * @return S
	 */
	protected function s() {return dfc($this, function() {return S::conventionB(static::class);});}
}