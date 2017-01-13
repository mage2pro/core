<?php
namespace Df\StripeClone;
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
	 * @return Card
	 */
	final public function card() {return dfc($this, function() {return
		df_newa(df_con($this, 'Card', Card::class), Card::class, $this->_card())
	;});}

	/**
	 * 2017-01-13
	 * @return string
	 */
	final public function id() {return $this['id'];}

	/**
	 * 2017-01-13
	 * @used-by card()
	 * @used-by country()
	 * @param string|null $key [optional]
	 * @return mixed|array(string => mixed)|null
	 */
	private function _card($key = null) {return $this->a(df_cc_path($this->keyCard(), $key));}
}

