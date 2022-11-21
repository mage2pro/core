<?php
namespace Df\API;
use Df\Core\Exception as DFE;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
/**
 * 2019-03-13
 * @see \Df\Payment\Settings
 * @see \Dfe\Sift\Settings
 * @see \Inkifi\Pwinty\Settings
 */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2019-03-13
	 * @used-by self::key()
	 * @see \Df\Payment\Settings::titleB()
	 */
	protected function titleB():string {return df_class_second($this);}

	/**
	 * 2017-04-12
	 * 2017-04-16 The «Robokassa» PSP use the same merchant identifier in the test and live modes.
	 * 2017-09-02 The «QIWI Wallet» PSP does not provide a test mode: https://mage2.pro/t/4443
	 * @used-by ikf_pw_api()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Dfe\AlphaCommerceHub\API\Client::commonParams()
	 * @used-by \Dfe\Klarna\Api\Checkout::html()
	 * @used-by \Dfe\Qiwi\API\Client::urlBase()
	 * @used-by \Dfe\Qiwi\Charge::pRedirect()
	 * @used-by \Dfe\Robokassa\Api\Options::p()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\Sift\API\Facade\GetDecisions::path()
	 * @used-by \Dfe\Vantiv\API\Client::_construct()
	 * @used-by \Dfe\Vantiv\Charge::pCharge()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
	 * @used-by \Dfe\YandexKassa\Signer::sign()
	 * @used-by \Inkifi\Pwinty\API\Client::headers()
	 * @param null|string|int|S|Store $s [optional]
	 */
	final function merchantID($s = null):string {return df_result_sne($this->probablyTestable(null, $s));}

	/**
	 * 2017-02-08
	 * @uses probablyTestableP()
	 * @used-by ikf_pw_api()
	 * @used-by \Df\GingerPaymentsBase\Settings::api()
	 * @used-by \Dfe\AlphaCommerceHub\API\Client::commonParams()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\CheckoutCom\Settings::api()
	 * @used-by \Dfe\Dragonpay\Signer::sign()
	 * @used-by \Dfe\IPay88\Signer::sign()
	 * @used-by \Dfe\Omise\Settings::init()
	 * @used-by \Dfe\Paymill\Test\Charge::t01()
	 * @used-by \Dfe\Spryng\Settings::api()
	 * @used-by \Dfe\Stripe\FE\Currency::hasKey()
	 * @used-by \Dfe\Stripe\Settings::init()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @used-by \Dfe\Vantiv\Charge::pCharge()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
	 * @used-by \Dfe\YandexKassa\Signer::sign()
	 * @used-by \Inkifi\Pwinty\API\Client::headers()
	 * @param null|string|int|S|Store $s [optional]
	 * @param bool $throw [optional]
	 * @return string|null
	 */
	final function privateKey($s = null, $throw = true) {return $this->key(
		'probablyTestableP', 'private', 'secret', $s, $throw
	);}

	/**
	 * 2016-11-12
	 * @used-by \Df\StripeClone\ConfigProvider::config()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\Vantiv\Charge::pCharge()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
	 * @uses self::probablyTestable()
	 * @see \Dfe\Square\Settings::publicKey()
	 * @see \Dfe\TBCBank\Settings::publicKey()
	 */
	function publicKey():string {return $this->key('probablyTestable', 'public', 'publishable');}

	/**
	 * 2016-03-02
	 * @used-by ikf_pw_api()
	 * @used-by self::testableGeneric()
	 * @used-by \Df\GingerPaymentsBase\Settings::options()
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\Method::test()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Dfe\CheckoutCom\Settings::api()
	 * @used-by \Dfe\Klarna\Api\Checkout::_html()
	 * @used-by \Dfe\Moip\ConfigProvider::config()
	 * @used-by \Dfe\Robokassa\Charge::pCharge()
	 * @used-by \Dfe\Spryng\Settings::api()
	 * @used-by \Inkifi\Pwinty\API\Client::urlBase()
	 * @param null|string|int|S $s [optional]
	 */
	final function test($s = null):bool {return $this->b(null, $s);}

	/**
	 * 2016-11-12
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @uses self::v()
	 * @used-by \Dfe\AlphaCommerceHub\Settings::apiDomain()
	 * @used-by \Dfe\AlphaCommerceHub\Settings::payPagePath()
	 * @used-by \Dfe\PostFinance\Settings::hashAlgorithm()
	 * @used-by \Dfe\Spryng\Settings::account()
	 * @used-by \Dfe\Square\Settings::location()
	 * @used-by \Dfe\Square\Settings::publicKey()
	 * @used-by \Dfe\TwoCheckout\Settings::accountNumber()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return mixed
	 */
	final protected function testable($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), 'v', $s, $d
	);}

	/**
	 * 2016-12-24
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses b()
	 */
	final protected function testableB($k = null, $s = null, $d = null):bool {return $this->testableGeneric(
		$k ?: df_caller_f(), 'b', $s, $d
	);}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае, когда значение шифруется как в промышленном, так и в тестовом режимах.
	 * Если значение шифруется только в промышленном режиме, то используйте @see self::testablePV().
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @uses \Df\Payment\Settings::p()
	 * @used-by \Df\StripeClone\Settings::privateKey()
	 * @used-by \Dfe\Klarna\Settings::sharedSecret()
	 * @used-by \Dfe\Moip\Settings::privateToken()
	 * @used-by \Dfe\Robokassa\Settings::password1()
	 * @used-by \Dfe\Robokassa\Settings::password2()
	 * @used-by \Dfe\Sift\Settings::backendKey()
	 * @used-by \Dfe\Sift\Settings::frontendKey()
	 * @used-by \Dfe\Square\Settings::accessToken()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @used-by \Dfe\TwoCheckout\Settings::secretWord()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return mixed
	 */
	final protected function testableP($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), 'p', $s, $d
	);}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае, когда значение шифруется в промышленном режиме, но не шифруется в тестовом.
	 * Если значение шифруется в обоих режимах, то используйте @see self::testableP().
	 * @used-by \Dfe\AllPay\Settings::hashIV()
	 * @used-by \Dfe\AllPay\Settings::hashKey()
	 * @used-by \Dfe\SecurePay\Settings::transactionPassword()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses self::p()
	 * @return mixed
	 */
	final protected function testablePV($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), ['p', 'v'], $s, $d
	);}

	/**
	 * 2017-02-08
	 * @used-by self::privateKey()
	 * @used-by self::publicKey()
	 * @uses self::testable()
	 * @uses self::testableP()
	 * @param string $method
	 * @param string $type
	 * @param string $alt
	 * @param null|string|int|S|Store $s [optional]
	 * @param bool $throw [optional]
	 * @return string|null
	 * @throws DFE
	 */
	private function key($method, $type, $alt, $s = null, $throw = true) {return
		$this->$method("{$type}Key", $s, function() use($method, $alt, $s) {return
			$this->$method("{$alt}Key", $s);}
		) ?: ($throw ? df_error("Please set your {$this->titleB()} $type key in the Magento backend.") : null)
	;}

	/**
	 * 2017-04-16
	 * Cначала мы пробуем найти значение с приставкой test/live, а затем без приставки.
	 * https://english.stackexchange.com/a/200637
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @used-by self::merchantID()
	 * @used-by self::publicKey()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses self::v()
	 * @return mixed
	 */
	private function probablyTestable($k = null, $s = null, $d = null) {
		$k = $k ?: df_caller_f();
		return $this->testableGeneric($k, 'v', $s, function() use($k, $s, $d) {return $this->v($k, $s, $d);});
	}

	/**
	 * 2017-10-02
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @used-by self::privateKey()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses self::v()
	 * @return mixed
	 */
	private function probablyTestableP($k = null, $s = null, $d = null) {
		$k = $k ?: df_caller_f();
		return $this->testableGeneric($k, 'p', $s, function() use($k, $s, $d) {return $this->p($k, $s, $d);});
	}

	/**
	 * 2016-11-12
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @used-by self::probablyTestable()
	 * @used-by self::testable()
	 * @used-by self::testableB()
	 * @used-by self::testableP()
	 * @used-by self::testablePV()
	 * @uses \Df\Config\Settings::p()
	 * @uses \Df\Config\Settings::v()
	 * @param string|null $k [optional]
	 * @param string|string[] $f [optional]
	 * $f может быть массивом,
	 * и тогда первое значение его — метод для промышленного режима, а второе значение — метод для тестового режима.
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return mixed
	 */
	private function testableGeneric($k = null, $f = 'v', $s = null, $d = null) {return call_user_func(
		[$this, is_string($f) ? $f : $f[intval($this->test($s))]]
		,($this->test($s) ? 'test' : 'live') . self::phpNameToKey(ucfirst($k ?: df_caller_f()))
		,$s, $d
	);}
}