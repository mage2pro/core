<?php
namespace Df\Payment\Settings;
// 2019-01-14
/** @used-by \Dfe\TBCBank\Settings::proxy() */
final class Proxy extends \Df\Payment\Settings {
	/**
	 * 2019-01-14
	 * @return string
	 */
	function enable() {return $this->b();}

	/**
	 * 2019-01-14
	 * @return string
	 */
	function host() {return $this->v();}

	/**
	 * 2019-01-14
	 * @return string
	 */
	function password() {return $this->p();}
	
	/**
	 * 2019-01-14
	 * @return string
	 */
	function port() {return $this->v();}

	/**
	 * 2019-01-14
	 * @return string
	 */
	function username() {return $this->v();}

	/**
	 * 2019-01-14
	 * @override
	 * @see \Df\Payment\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return dfc($this, function() {return parent::prefix() . '/proxy';});}
}