<?php
namespace Df\Framework\Form\Element\Enable\Requirement;
use Df\Framework\Form\Element\Enable\Requirement;
class Currency extends Requirement {
	/**
	 * 2016-06-30
	 * @param string $iso3
	 */
	public function __construct($iso3) {$this->_iso3 = $iso3;}

	/**
	 * 2016-06-30
	 * @override
	 * @see \Df\Framework\Form\Element\Enable\Requirement::check()
	 * @return true|string
	 */
	public function check() {return df_currency_has_rate($this->_iso3) ?: $this->message();}

	/**
	 * @return string
	 */
	private function message() {
		return df_currency_ctn($this->_iso3);
	}

	/**
	 * 2016-06-30
	 * @var string
	 */
	private $_iso3;
}