<?php
namespace Df\PaypalClone;
/**
 * 2016-07-10
 * @see \Dfe\AllPay\Signer
 * @see \Dfe\SecurePay\Signer
 */
abstract class Signer {
	/**
	 * 2016-07-10
	 * @used-by _sign()
	 * @see \Dfe\AllPay\Signer::sign()
	 * @see \Dfe\SecurePay\Signer::sign()
	 * @return string
	 */
	abstract protected function sign();

	/**
	 * 2016-08-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\SecurePay\Signer\Response::req()
	 * @return object
	 */
	protected function caller() {return $this->_caller;}

	/**
	 * 2017-03-13            
	 * @used-by \Dfe\AllPay\Signer::sign()         
	 * @used-by \Dfe\SecurePay\Signer\Request::values()
	 * @used-by \Dfe\SecurePay\Signer\Response::values()
	 * @param string|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function v($k = null, $d = null) {return dfak($this->_v, $k, $d);}

	/**
	 * 2016-08-27
	 * @used-by _sign()
	 * @used-by caller()
	 * @var object
	 */
	private $_caller;

	/**
	 * 2017-03-13   
	 * @used-by _sign()
	 * @used-by v()
	 * @var array(string => mixed)
	 */
	private $_v;

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @param object $caller
	 * @param array(string => mixed) $p
	 * @return string
	 */
	final static function signRequest($caller, array $p) {return self::_sign($caller, $p);}

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\W\Handler::validate()
	 * @param object $caller
	 * @param array(string => mixed) $p
	 * @return string
	 */
	final static function signResponse($caller, array $p) {return self::_sign($caller, $p);}

	/**
	 * 2016-08-27
	 * @used-by signRequest()
	 * @used-by signResponse()
	 * @param object $caller
	 * @param array(string => mixed) $v
	 * @return string
	 */
	private static function _sign($caller, array $v) {
		/** @var string $type */
		$type = df_trim_text_left(df_caller_f(), 'sign');
		/** @var self $i */
		$i = df_new(df_con($caller, df_cc_class('Signer', $type), df_con($caller, 'Signer')));
		$i->_caller = $caller;
		$i->_v = $v;
		return $i->sign();
	}
}