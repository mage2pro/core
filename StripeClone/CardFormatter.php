<?php
namespace Df\StripeClone;
use Df\StripeClone\Facade\ICard as C;
// 2017-02-11
final class CardFormatter {
	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\ResponseRecord::card()
	 * @param C $c
	 */
	public function __construct(C $c) {$this->_c = $c;}

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return string
	 */
	public function country() {return df_country_ctn(strtoupper($this->_c->country()));}

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return string
	 */
	public function exp() {return implode(' / ', [
		sprintf('%02d', $this->_c->expMonth()), $this->_c->expYear()
	]);}

	/**          
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @return string
	 */
	public function label() {return "···· {$this->_c->last4()} ({$this->_c->brand()})";}

	/**
	 * 2017-02-11
	 * @var C
	 */
	private $_c;
}