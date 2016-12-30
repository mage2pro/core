<?php
// 2016-12-28
namespace Df\StripeClone;
abstract class Charge extends \Df\Payment\Charge\WithToken {
	/**
	 * 2016-12-28
	 * @used-by request()
	 * @return array(string => mixed)
	 */
	abstract protected function _request();

	/**
	 * 2016-12-28
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__NEED_CAPTURE, DF_V_BOOL, false);
	}

	/** @return bool */
	final protected function needCapture() {return $this[self::$P__NEED_CAPTURE];}

	/**
	 * 2016-12-28
	 * @used-by \Dfe\Stripe\Method::charge()
	 * @param Method $method
	 * @param string $token
	 * @param float|null $amount [optional]
	 * @param bool $capture [optional]
	 * @return array(string => mixed)
	 */
	public static function request(Method $method, $token, $amount = null, $capture = true) {return
		(new static([
			self::$P__AMOUNT => $amount
			,self::$P__NEED_CAPTURE => $capture
			,self::$P__METHOD => $method
			,self::$P__TOKEN => $token
		]))->_request();
	}

	/** @var string */
	private static $P__NEED_CAPTURE = 'need_capture';
}