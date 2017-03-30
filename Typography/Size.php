<?php
namespace Df\Typography;
class Size extends \Df\Core\O {
	/**
	 * 2015-12-16
	 * @override
	 */
	function __toString() {return "{$this->value()}{$this->units()}";}

	/** @return string */
	function units() {return $this[self::$P__UNITS];}

	/** @return string */
	function value() {return $this[self::$P__VALUE];}

	/**
	 * 2015-12-16
	 * @return float
	 */
	function valueF() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_float($this->value());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * @return int
	 */
	function valueI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = intval($this->value());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * @override
	 * @see \Df\Core\O::_construct()
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__UNITS, DF_V_STRING_NE)
			->_prop(self::$P__VALUE, DF_V_STRING)
		;
	}
	/** @var string */
	private static $P__UNITS = 'units';
	/** @var string */
	private static $P__VALUE = 'value';
}