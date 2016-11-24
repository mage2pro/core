<?php
// 2016-11-24
namespace Df\Config\Settings;
class Configurable extends \Df\Config\Settings {
	/**
	 * 2016-11-24
	 * @override
	 * @param string $prefix
	 */
	public function __construct($prefix) {
		df_param_string_not_empty($prefix, 0);
		$this->_prefix = $prefix;
	}

	/**
	 * 2016-11-24
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return $this->_prefix;}

	/** @var string */
	private $_prefix;
}