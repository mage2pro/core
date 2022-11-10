<?php
namespace Df\PaypalClone;
use Df\Payment\IMA;
use Df\Payment\Settings;
/**
 * 2016-07-10
 * @see \Dfe\AllPay\Signer
 * @see \Dfe\Dragonpay\Signer
 * @see \Dfe\IPay88\Signer
 * @see \Dfe\PostFinance\Signer
 * @see \Dfe\Qiwi\Signer
 * @see \Dfe\Robokassa\Signer
 * @see \Dfe\SecurePay\Signer
 * @see \Dfe\YandexKassa\Signer
 */
abstract class Signer {
	/**
	 * 2016-07-10
	 * @used-by self::_sign()
	 * @see \Dfe\AllPay\Signer::sign()
	 * @see \Dfe\Dragonpay\Signer::sign()
	 * @see \Dfe\IPay88\Signer::sign()
	 * @see \Dfe\PostFinance\Signer::sign()
	 * @see \Dfe\Qiwi\Signer::sign()
	 * @see \Dfe\Robokassa\Signer::sign()
	 * @see \Dfe\SecurePay\Signer::sign()
	 * @see \Dfe\YandexKassa\Signer::sign()
	 */
	abstract protected function sign():string;

	/**
	 * 2017-03-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\Signer::sign()
	 * @used-by \Dfe\Dragonpay\Signer::sign()
	 * @used-by \Dfe\IPay88\Signer::sign()
	 * @used-by \Dfe\PostFinance\Signer::sign()
	 * @used-by \Dfe\Robokassa\Signer\Response::values()
	 * @used-by \Dfe\SecurePay\Signer::sign()
	 * @used-by \Dfe\SecurePay\Signer\Request::values()
	 */
	protected function s():Settings {return dfps($this);}

	/**
	 * 2017-03-13            
	 * @used-by \Dfe\AllPay\Signer::sign()
	 * @used-by \Dfe\Dragonpay\Signer\Request::values()
	 * @used-by \Dfe\Dragonpay\Signer\Response::values()
	 * @used-by \Dfe\IPay88\Signer::sign()
	 * @used-by \Dfe\IPay88\Signer\Request::values()
	 * @used-by \Dfe\IPay88\Signer\Response::values()
	 * @used-by \Dfe\PostFinance\Signer::sign()
	 * @used-by \Dfe\Qiwi\Signer::sign()
	 * @used-by \Dfe\Robokassa\Signer\Request::values()
	 * @used-by \Dfe\Robokassa\Signer\Response::values()
	 * @used-by \Dfe\SecurePay\Signer\Request::values()
	 * @used-by \Dfe\SecurePay\Signer\Response::values()
	 * @used-by \Dfe\YandexKassa\Signer::sign()
	 * @param string|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function v($k = null, $d = null) {return dfa($this->_v, $k, $d);}

	/**
	 * 2017-03-13   
	 * @used-by self::_sign()
	 * @used-by self::v()
	 * @var array(string => mixed)
	 */
	private $_v;

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @param IMA $caller
	 * @param array(string => mixed) $p
	 */
	final static function signRequest(IMA $caller, array $p):string {return self::_sign($caller, $p);}

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\W\Event::validate()
	 * @param IMA $caller
	 * @param array(string => mixed) $p
	 */
	final static function signResponse(IMA $caller, array $p):string {return self::_sign($caller, $p);}

	/**
	 * 2017-04-10
	 * @used-by self::_sign()
	 * @see \Dfe\IPay88\Signer::adjust()
	 * @param array(string => mixed) $v
	 * @return array(string => mixed)
	 */
	protected function adjust(array $v):array {return $v;}

	/**
	 * 2016-08-27
	 * @used-by self::signRequest()
	 * @used-by self::signResponse()
	 * @param IMA $caller
	 * @param array(string => mixed) $v
	 */
	private static function _sign(IMA $caller, array $v):string {/** @var self $i */
		$i = df_new(df_con_hier_suf_ta($caller->m(), 'Signer', df_trim_text_left(df_caller_f(), 'sign')));
		$i->_v = $i->adjust($v);
		return $i->sign();
	}
}