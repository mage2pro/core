<?php
namespace Df\StripeClone;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Card;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-13
 * @see \Dfe\Omise\ResponseRecord
 * @see \Dfe\Stripe\ResponseRecord
 */
abstract class ResponseRecord extends \Df\Core\A {
	/**
	 * 2017-01-13
	 * @used-by _card()
	 * @return string
	 */
	abstract protected function keyCard();

	/**
	 * 2017-01-13
	 * @return CF
	 */
	final public function card() {return dfc($this, function() {return new CF(
		Card::create($this, $this->a(df_cc_path($this->keyCard())))
	);});}

	/**
	 * 2017-01-13
	 * @return string
	 */
	final public function id() {return $this['id'];}
}

