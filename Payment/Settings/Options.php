<?php
namespace Df\Payment\Settings;
use Df\Config\Source;
use Df\Payment\Settings as S;
use Df\Payment\Source\Options\Location as OL;
# 2017-03-03
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
	 * @used-by self::denied()
	 * @used-by self::o()
	 * @used-by \Dfe\AllPay\Charge::isSingleOptionChosen()
	 * @used-by \Dfe\AllPay\Charge::pChoosePayment()
	 * @return string[]
	 */
	function allowed():array {return $this->_s->csv('optionsAllowed');}

	/**
	 * 2017-03-03
	 * @used-by \Dfe\AllPay\Charge::pIgnorePayment()
	 * @return string[]
	 */
	function denied():array {return array_diff($this->_source->keys(), $this->allowed());}

	/**
	 * 2016-07-05 «Limit the payment options availability?»
	 * @used-by self::o()
	 * @used-by \Dfe\AllPay\Charge::pChoosePayment()
	 * @used-by \Dfe\AllPay\Charge::pIgnorePayment()
	 */
	function isLimited():bool {return $this->_s->b('optionsLimit');}

	/**
	 * 2016-08-07
	 * @used-by \Df\GingerPaymentsBase\Settings::options()
	 * @used-by \Dfe\AllPay\ConfigProvider::config()
	 * @used-by \Dfe\YandexKassa\ConfigProvider::options()
	 * @param bool $force [optional]
	 * The `true` value is passed by:
	 * @used-by \Dfe\AllPay\ConfigProvider::config()
	 * @used-by \Dfe\IPay88\ConfigProvider::options()
	 * @used-by \Dfe\YandexKassa\ConfigProvider::options()
	 * @return array(<value> => <label>)
	 */
	function o($force = false):array {return !self::needShow($this->_s) && !$force ? [] : $this->_source->options(
		!$this->isLimited() ? null : $this->allowed()
	);}

	/**
	 * 2017-09-19 «Where to ask for a payment option?»
	 * @used-by self::o()
	 * @used-by \Df\Payment\ConfigProvider::configOptions()
	 * @param S $s
	 */
	static function needShow(S $s):bool {return OL::MAGENTO === $s->v('optionsLocation', null, OL::MAGENTO);}

	/**
	 * 2017-03-03
	 * @used-by self::__construct()
	 * @used-by self::needShow()
	 * @var S
	 */
	private $_s;

	/**
	 * 2017-03-03
	 * @used-by self::__construct()
	 * @var Source
	 */
	private $_source;
}