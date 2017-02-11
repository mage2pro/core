<?php
namespace Df\Config\Settings;
/**
 * 2016-11-24
 * @see \Df\Sso\Settings\Button
 */
class Configurable extends \Df\Config\Settings {
	/**
	 * 2017-01-27
	 * @used-by \Df\Sso\Button::s()
	 * @param string $prefix
	 */
	final function __construct($prefix) {$this->_prefix = $prefix;}

	/**
	 * 2016-11-24
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	final protected function prefix() {return $this->_prefix;}

	/**
	 * 2017-01-27
	 * @used-by __construct()
	 * @used-by prefix()
	 * @var string
	 */
	private $_prefix;
}