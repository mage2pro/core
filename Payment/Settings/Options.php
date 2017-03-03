<?php
namespace Df\Payment\Settings;
use Df\Config\SourceT;
use Df\Payment\Settings as S;
// 2017-03-03
final class Options {
	/**
	 * 2017-03-03
	 * @used-by \Dfe\AllPay\Settings::options()
	 * @param S $s  
	 * @param SourceT $source
	 */
	function __construct(S $s, SourceT $source) {$this->_s = $s; $this->_source = $source;}

	/**
	 * 2016-07-05
	 * «Allowed Payment Options»
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
	 * 2016-07-05
	 * «Limit Payment Options Availability?»
	 * @used-by o()
	 * @used-by \Dfe\AllPay\Charge::pChoosePayment()
	 * @used-by \Dfe\AllPay\Charge::pIgnorePayment()
	 * @return bool
	 */
	function isLimited() {return $this->_s->b('optionsLimit');}

	/**
	 * 2016-08-07
	 * @used-by \Dfe\AllPay\ConfigProvider::config()
	 * @return array(string => string)
	 */
	function o() {return $this->_source->options(!$this->isLimited() ? null : $this->allowed());}

	/**
	 * 2017-03-03
	 * @used-by __construct()
	 * @var S
	 */
	private $_s;

	/**
	 * 2017-03-03
	 * @used-by __construct()
	 * @var SourceT
	 */
	private $_source;
}