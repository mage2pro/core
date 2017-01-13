<?php
namespace Df\StripeClone;
/**
 * 2017-01-13
 * @see \Dfe\Omise\Card
 * @see \Dfe\Stripe\Card
 */
abstract class Card extends \Df\Core\O {
	/**
	 * 2017-01-13
	 * @used-by __toString()
	 * @see \Dfe\Omise\Card::keyLast4()
	 * @see \Dfe\Stripe\Card::keyLast4()
	 * @return string
	 */
	abstract protected function keyLast4();

	/**
	 * 2017-01-13
	 * @used-by expires()
	 * @see \Dfe\Omise\Card::prefixKeyExpiration()
	 * @see \Dfe\Stripe\Card::prefixKeyExpiration()
	 * @return string
	 */
	abstract protected function prefixKeyExpiration();

	/**
	 * 2017-01-13
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Dfe\Omise\Api\Customer::_cards()
	 * @used-by \Dfe\Stripe\ApiCustomer::cards()
	 * @return string
	 */
	final public function __toString() {return "···· {$this[$this->keyLast4()]} ({$this['brand']})";}

	/**
	 * 2017-01-13
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return string
	 */
	final public function country() {return df_country_ctn(strtoupper($this['country']));}

	/**
	 * 2017-01-13
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return string
	 */
	final public function expires() {
		/** @var string $p */
		$p = $this->prefixKeyExpiration();
		return implode(' / ', [sprintf('%02d', $this["{$p}_month"]), $this["{$p}_year"]]);
	}
}