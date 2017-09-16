<?php
namespace Df\Payment\Settings;
use Df\Config\Source;
use Df\Payment\Settings as S;
use Df\PaypalClone\Source\OptionsLocation as OL;
// 2017-03-03
final class Options {
	/**
	 * 2017-03-03
	 * @used-by \Df\Payment\Settings::_options()
	 * @param S $s  
	 * @param Source $source
	 */
	function __construct(S $s, Source $source) {$this->_s = $s; $this->_source = $source;}

	/**
	 * 2016-07-05 «Allowed Payment Options»
	 * @used-by denied()
	 * @used-by o()
	 * @used-by \Dfe\AllPay\Charge::isSingleOptionChosen()
	 * @used-by \Dfe\AllPay\Charge::pChoosePayment()
	 * @return string[]
	 */
	function allowed() {return $this->_s->csv('optionsAllowed');}

	/**
	 * 2017-03-03
	 * @used-by \Dfe\AllPay\Charge::pIgnorePayment()
	 * @return string[]
	 */
	function denied() {return array_diff($this->_source->keys(), $this->allowed());}

	/**
	 * 2016-07-05 «Limit the payment options availability?»
	 * @used-by o()
	 * @used-by \Dfe\AllPay\Charge::pChoosePayment()
	 * @used-by \Dfe\AllPay\Charge::pIgnorePayment()
	 * @return bool
	 */
	function isLimited() {return $this->_s->b('optionsLimit');}

	/**
	 * 2017-09-17 «Where to ask for a payment option?»
	 * @used-by o()
	 * @return bool
	 */
	function needShow() {return OL::MAGENTO === $this->_s->v('optionsLocation');}

	/**
	 * 2016-08-07
	 * @used-by \Df\GingerPaymentsBase\Settings::options()
	 * @used-by \Dfe\AllPay\ConfigProvider::config()
	 * @used-by \Dfe\YandexKassa\ConfigProvider::config()
	 * @param bool $force [optional]
	 * The `true` value is passed by @used-by \Dfe\AllPay\ConfigProvider::config()
	 * @return array(string => string)
	 */
	function o($force = false) {return !$this->needShow() && !$force ? [] : $this->_source->options(
		!$this->isLimited() ? null : $this->allowed()
	);}

	/**
	 * 2017-03-03
	 * @used-by __construct()
	 * @used-by needShow()
	 * @var S
	 */
	private $_s;

	/**
	 * 2017-03-03
	 * @used-by __construct()
	 * @var Source
	 */
	private $_source;
}