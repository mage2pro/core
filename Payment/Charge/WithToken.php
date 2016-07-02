<?php
namespace Df\Payment\Charge;
use Df\Payment\Charge;
// 2016-07-02
abstract class WithToken extends Charge {
	/** @return string */
	protected function token() {return $this[self::$P__TOKEN];}

	/**
	 * 2016-07-02
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__TOKEN, RM_V_STRING_NE);
	}
	/** @var string */
	protected static $P__TOKEN = 'token';
}