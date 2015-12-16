<?php
namespace Df\Typography;
class Size extends \Df\Core\O {
	/**
	 * 2015-12-16
	 * @override
	 */
	public function __toString() {return "{$this->value()}{$this->units()}";}

	/** @return string */
	public function units() {return $this[self::$P__UNITS];}

	/** @return string */
	public function value() {return $this[self::$P__VALUE];}

	/**
	 * 2015-12-16
	 * @return float
	 */
	public function valueF() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_float($this->value());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * @return int
	 */
	public function valueI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = intval($this->value());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__UNITS, RM_V_STRING_NE)
			->_prop(self::$P__VALUE, RM_V_STRING)
		;
	}
	/** @var string */
	private static $P__UNITS = 'units';
	/** @var string */
	private static $P__VALUE = 'value';
}