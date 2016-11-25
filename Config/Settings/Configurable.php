<?php
// 2016-11-24
namespace Df\Config\Settings;
class Configurable extends \Df\Config\Settings {
	/**
	 * 2016-11-24
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return $this[self::PREFIX];}

	/**
	 * 2016-11-25
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::PREFIX, DF_V_STRING_NE);
	}

	/**
	 * 2016-11-25
	 * @used-by \Df\Sso\Button::s()
	 */
	const PREFIX = 'prefix';
}