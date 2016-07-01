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
	 * 2016-06-30
	 * @return string
	 */
	private function message() {
		/** @var string $name */
		$name = df_currency_ctn($this->_iso3);
		/** @var string $urlEnable */
		$urlEnable = df_url_backend('admin/system_config/edit/section/currency');
		/** @var string $urlRate */
		$urlRate = df_url_backend('admin/system_currency');
		return "Please <a href='{$urlEnable}' target='_blank'>enable</a> the «<b>{$name}</b>» currency"
	   	. " and <a href='{$urlRate}' target='_blank'>set an exchange rate</a> for it.";
	}

	/**
	 * 2016-06-30
	 * @var string
	 */
	private $_iso3;
}