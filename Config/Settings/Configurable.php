<?php
namespace Df\Config\Settings;
# 2016-11-24
/** @see \Df\Sso\Settings\Button */
class Configurable extends \Df\Config\Settings {
	/**
	 * 2017-01-27
	 * @override
	 * @see \Df\Config\Settings::__construct()
	 * @used-by \Df\Sso\Button::s()
	 */
	final function __construct(string $prefix) {$this->_prefix = $prefix;}

	/**
	 * 2016-11-24
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 */
	final protected function prefix():string {return $this->_prefix;}

	/**
	 * 2017-01-27
	 * @used-by self::__construct()
	 * @used-by self::prefix()
	 * @var string
	 */
	private $_prefix;
}