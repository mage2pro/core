<?php
namespace Df\PaypalClone\Init;
use Df\PaypalClone\Charge;
/**
 * 2017-03-21
 * @see \Dfe\AllPay\Init\Action
 * @see \Dfe\AlphaCommerceHub\Init\Action
 * @see \Dfe\Dragonpay\Init\Action
 * @see \Dfe\IPay88\Init\Action
 * @see \Dfe\PostFinance\Init\Action
 * @see \Dfe\Robokassa\Init\Action
 * @see \Dfe\SecurePay\Init\Action
 * @see \Dfe\YandexKassa\Init\Action
 * @method \Df\PaypalClone\Method m()
 */
abstract class Action extends \Df\Payment\Init\Action {
	/**
	 * 2017-03-21
	 * @override
	 * @see \Df\Payment\Init\Action::redirectParams()
	 * @used-by \Df\Payment\Init\Action::action()
	 * @return array(string => mixed)
	 */
	final protected function redirectParams():array {return df_last($this->charge());}

	/**
	 * 2017-03-21
	 * @override
	 * @see \Df\Payment\Init\Action::transId()
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by self::action()
	 */
	final protected function transId():string {return $this->e2i(df_first($this->charge()));}

	/**
	 * 2017-03-21
	 * @used-by self::redirectParams()
	 * @used-by self::transId()
	 * @return array(string, array(string => mixed))
	 */
	private function charge():array {return dfc($this, function() {return Charge::p($this->m());});}
}