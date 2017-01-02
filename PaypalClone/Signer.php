<?php
namespace Df\PaypalClone;
abstract class Signer extends \Df\Core\O {
	/**
	 * 2016-07-10
	 * @used-by \Df\PaypalClone\Signer::_sign()
	 * @return string
	 */
	abstract protected function sign();

	/**
	 * 2016-08-27
	 * @return object
	 */
	protected function caller() {return $this->_caller;}

	/**
	 * 2016-08-27
	 * @var object
	 */
	private $_caller;

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @param object $caller
	 * @param array(string => mixed) $p
	 * @return string
	 */
	final public static function signRequest($caller, array $p) {return self::_sign($caller, $p);}

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Webhook::validate()
	 * @param object $caller
	 * @param array(string => mixed) $p
	 * @return string
	 */
	final public static function signResponse($caller, array $p) {return self::_sign($caller, $p);}

	/**
	 * 2016-08-27
	 * @param object $caller
	 * @param array(string => mixed) $p
	 * @return string
	 */
	private static function _sign($caller, array $p) {
		/** @var string $type */
		$type = df_trim_text_left(df_caller_f(), 'sign');
		/** @var self $i */
		$i = df_create(df_con($caller, df_cc_class('Signer', $type), df_con($caller, 'Signer')), $p);
		$i->_caller = $caller;
		return $i->sign();
	}
}