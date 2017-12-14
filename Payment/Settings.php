<?php
namespace Df\Payment;
use Df\Config\Source as ConfigSource;
use Df\Core\Exception as DFE;
use Df\Payment\Method as M;
use Df\Payment\Settings\Options;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Payment\Model\Checks\TotalMinMax as T;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;
/**
 * 2017-02-15
 * @see \Df\GingerPaymentsBase\Settings
 * @see \Df\Payment\Settings\BankCard
 * @see \Dfe\AllPay\Settings
 * @see \Dfe\AlphaCommerceHub\Settings
 * @see \Dfe\AlphaCommerceHub\Settings\Card
 * @see \Dfe\Dragonpay\Settings
 * @see \Dfe\IPay88\Settings
 * @see \Dfe\Klarna\Settings
 * @see \Dfe\Moip\Settings\Boleto
 * @see \Dfe\MPay24\Settings
 * @see \Dfe\Paypal\Settings
 * @see \Dfe\Paystation\Settings
 * @see \Dfe\PostFinance\Settings
 * @see \Dfe\Qiwi\Settings
 * @see \Dfe\Robokassa\Settings
 * @see \Dfe\Tinkoff\Settings
 * @see \Dfe\YandexKassa\Settings
 */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2017-03-27
	 * @used-by \Df\Payment\Method::s()
	 * @used-by \Dfe\AlphaCommerceHub\Settings::card()
	 * @used-by \Dfe\Moip\Settings::boleto()
	 * @param M $m
	 */
	final function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-12-13
	 * 1) "Provide an ability to the Magento backend users (merchants) to set up country restrictions separately
	 * for each AlphaCommerceHub's payment option (bank cards, PayPal, POLi Payments, etc.)":
	 * https://github.com/mage2pro/alphacommercehub/issues/85
	 * 2) It is implemented by analogy with @see \Magento\Payment\Model\Checks\CanUseForCountry::isApplicable()
	 * @used-by \Dfe\AlphaCommerceHub\ConfigProvider::option()
	 * @param string $option
	 * @return boolean
	 */
	final function applicableForQuoteByCountry($option) {return $this->m()->canUseForCountryP(
		df_oq_country_sb(df_quote()), $option
	);}

	/**
	 * 2017-07-29
	 * It is implemented by analogy with @see \Magento\Payment\Model\Checks\TotalMinMax::isApplicable()
	 * @used-by \Dfe\AlphaCommerceHub\ConfigProvider::option()
	 * @used-by \Dfe\Moip\ConfigProvider::config()
	 * @param string $option
	 * @return boolean
	 */
	final function applicableForQuoteByMinMaxTotal($option) {
		$a = df_quote()->getBaseGrandTotal(); /** @var float $a */
        $max = $this->v("$option/" . T::MAX_ORDER_TOTAL); /** @var float $max */
		$min = $this->v("$option/" . T::MIN_ORDER_TOTAL); /** @var float $min */
		return !($min && $a < $min || $max && $a > $max);
	}

	/**
	 * 2016-11-16
	 * «Description»                                
	 * @used-by \Df\Payment\Charge::description()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return string
	 */
	final function description() {return $this->v();}

	/**
	 * 2016-03-14
	 * 2017-02-18
	 * «Dynamic statement descripor»
	 * https://mage2.pro/tags/dynamic-statement-descriptor
	 * https://stripe.com/blog/dynamic-descriptors
	 * https://support.stripe.com/questions/does-stripe-support-dynamic-descriptors
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @return string
	 */
	final function dsd() {return $this->v(null, null, function() {return $this->v('statement');});}

	/**
	 * 2016-12-27
	 * 2017-01-10
	 * Инициализация библиотеки платёжной системы.
	 * Пока я использую такие библиотеки только для Stripe-подобных платёжных систем,
	 * и не использую для PayPal-подобных (allPay и SecurePay),
	 * просто потому что для PayPal-подобных они отсутствовали.
	 * Тем не менее, перенёс этот метод из класса настроек Stripe-подобных платёжных модулей
	 * в базовый класс настроек всех платёжных модулей
	 * потому что нам удобно вызывать этот метод из базового класса платёжных модулей:
	 * @used-by dfe_stripe_source()
	 * @used-by \Df\Payment\Method::action()
	 * @used-by \Df\Payment\W\Reader\Json::__construct()
	 * @see \Dfe\TwoCheckout\Settings::init()
	 * @see \Dfe\Omise\Settings::init()
	 * @see \Dfe\Stripe\Settings::init()
	 */
	function init() {}

	/**
	 * 2016-12-26
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @return bool
	 */
	final function log() {return $this->b(null, null, true);}

	/**
	 * 2017-04-12
	 * 2017-04-16 The «Robokassa» PSP use the same merchant identifier in the test and live modes.
	 * 2017-09-02 The «QIWI Wallet» PSP does not provide a test mode: https://mage2.pro/t/4443
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Dfe\AlphaCommerceHub\API\Client::commonParams()
	 * @used-by \Dfe\Klarna\Api\Checkout::html()
	 * @used-by \Dfe\Qiwi\API\Client::urlBase()
	 * @used-by \Dfe\Qiwi\Charge::pRedirect()
	 * @used-by \Dfe\Robokassa\Api\Options::p()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\YandexKassa\Signer::sign()
	 * @param null|string|int|S|Store $s [optional]
	 * @return string
	 */
	final function merchantID($s = null) {return df_result_sne($this->probablyTestable(null, $s));}

	/**
	 * 2016-08-27
	 * 2017-12-03
	 * "If a customer has failed a 3D Secure verification,
	 * then the extension incorrectly shows him an empty explanation message
	 * on his return to the Magento store: "The payment service's message is «»".":
	 * https://github.com/mage2pro/stripe/issues/56
	 * @used-by \Df\Payment\CustomerReturn::execute()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Dfe\CheckoutCom\Response::messageC()
	 * @param string|null $m [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string
	 */
	final function messageFailure($m = null, $s = null) {return df_var(
		$this->v(null, $s, function() use($m) {return df_cc_br(
			'Sorry, the payment attempt is failed.'
			,!$m ? null : "The payment service's message is «<b>{originalMessage}</b>»."
			,'Please try again, or try another payment method.'
		);})
		/**
		 * 2017-12-03
		 * The Checkout.com module uses the `message` key:
		 * @used-by \Dfe\CheckoutCom\Response::messageC()
		 */
		,array_fill_keys(['message', 'originalMessage'], $m)
	);}

	/**
	 * 2016-03-14
	 * @return string[]
	 */
	final function metadata() {return $this->csv();}

	/**
	 * 2017-02-08
	 * @uses probablyTestableP()
	 * @used-by \Df\GingerPaymentsBase\Settings::api()
	 * @used-by \Dfe\AlphaCommerceHub\API\Client::commonParams()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\CheckoutCom\Settings::api()
	 * @used-by \Dfe\Dragonpay\Signer::sign()
	 * @used-by \Dfe\IPay88\Signer::sign()
	 * @used-by \Dfe\Omise\Settings::init()
	 * @used-by \Dfe\Paymill\T\Charge::t01()
	 * @used-by \Dfe\Spryng\Settings::api()
	 * @used-by \Dfe\Stripe\FE\Currency::hasKey()
	 * @used-by \Dfe\Stripe\Settings::init()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @used-by \Dfe\YandexKassa\Signer::sign()
	 * @param null|string|int|S|Store $s [optional]
	 * @param bool $throw [optional]
	 * @return string|null
	 */
	final function privateKey($s = null, $throw = true) {return $this->key(
		'probablyTestableP', 'private', 'secret', $s, $throw
	);}

	/**
	 * 2016-11-12
	 * @uses probablyTestable()
	 * @see \Dfe\Square\Settings::publicKey()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @return string
	 */
	function publicKey() {return $this->key('probablyTestable', 'public', 'publishable');}

	/**
	 * 2016-07-27
	 * «Require the billing address?»
	 * If checked, Magento will require the billing address.
	 * It is the default Magento behavior.
	 * If unchecked, Magento will not require the billing address, and even will not ask for it.
	 * @see \Df\Customer\Settings\BillingAddress

	 * «The billing address is key for them to justify their purchase as a cost for their company»
	 * http://ux.stackexchange.com/a/60859
	 *
	 * «The billing address is for the invoice. If I buy something for personal use
	 * the invoice shouldn't have my company as recipient since I bought it, not the company.
	 * That difference can be important for accounting, taxation, debt collection and other legal reasons.»
	 * http://ux.stackexchange.com/questions/60846#comment94596_60859
	 *
	 * 2017-02-16 https://github.com/mage2pro/core/issues/8
	 *
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\Method::requireBillingAddress()
	 * @return bool
	 */
	final function requireBillingAddress() {return $this->b(null, null, function() {return
		$this->b('askForBillingAddress', null, true)
	;});}

	/**
	 * 2016-03-02
	 * @used-by testableGeneric()
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
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	final function test($s = null) {return $this->b(null, $s);}

	/**
	 * 2017-03-03
	 * @used-by \Df\GingerPaymentsBase\Settings::options()
	 * @used-by \Dfe\AllPay\Settings::options()
	 * @used-by \Dfe\AlphaCommerceHub\Settings::options()
	 * @used-by \Dfe\YandexKassa\Settings::options()
	 * @param string|ConfigSource $source
	 * @return Options
	 */
	final protected function _options($source) {return dfc($this, function($s) {return new Options(
		$this, is_object($s) ? $s : df_sc($s, ConfigSource::class)
	);}, func_get_args());}

	/**
	 * 2017-03-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by applicableForQuoteByCountry()
	 * @used-by \Df\GingerPaymentsBase\Settings::options()
	 * @used-by \Dfe\Moip\Settings::boleto()
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2016-08-25
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @see \Df\Payment\Settings\_3DS::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @used-by \Df\Payment\Settings\_3DS::prefix()
	 * @see \Dfe\Moip\Settings\Boleto::prefix()
	 * @return string
	 */
	protected function prefix() {return dfc($this, function() {return
		'df_payment/' . dfpm_code_short($this->_m)
	;});}

	/**
	 * 2017-03-27
	 * @override
	 * @see \Df\Core\Settings::scopeDefault()
	 * @used-by \Df\Core\Settings::scope()
	 * @used-by \Df\Payment\Settings\_3DS::scopeDefault()
	 * @return int|S|Store|null|string
	 */
	protected function scopeDefault() {return $this->_m->getStore();}

	/**
	 * 2016-11-12
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses v()
	 * @used-by \Dfe\AlphaCommerceHub\Settings::apiDomain()
	 * @used-by \Dfe\AlphaCommerceHub\Settings::payPagePath()
	 * @used-by \Dfe\PostFinance\Settings::hashAlgorithm()
	 * @used-by \Dfe\Spryng\Settings::account()
	 * @used-by \Dfe\Square\Settings::location()
	 * @used-by \Dfe\Square\Settings::publicKey()
	 * @used-by \Dfe\TwoCheckout\Settings::accountNumber()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
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
	 * @return bool
	 */
	final protected function testableB($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), 'b', $s, $d
	);}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае,
	 * когда значение шифруется как в промышленном, так и в тестовом режимах.
	 * @used-by \Df\StripeClone\Settings::privateKey()
	 * @used-by \Dfe\Klarna\Settings::sharedSecret()
	 * @used-by \Dfe\Moip\Settings::privateToken()
	 * @used-by \Dfe\Robokassa\Settings::password1()
	 * @used-by \Dfe\Robokassa\Settings::password2()
	 * @used-by \Dfe\Square\Settings::accessToken()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @used-by \Dfe\TwoCheckout\Settings::secretWord()
	 * Если значение шифруется только в промышленном режиме, то используйте @see testablePV()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses \Df\Payment\Settings::p()
	 * @return mixed
	 */
	final protected function testableP($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), 'p', $s, $d
	);}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае,
	 * когда значение шифруется в промышленном режиме, но не шифруется в тестовом.
	 * @used-by \Dfe\AllPay\Settings::hashIV()
	 * @used-by \Dfe\AllPay\Settings::hashKey()
	 * @used-by \Dfe\SecurePay\Settings::transactionPassword()
	 * Если значение шифруется в обоих режимах, то используйте @see testableP()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses p()
	 * @return mixed
	 */
	final protected function testablePV($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), ['p', 'v'], $s, $d
	);}

	/**
	 * 2017-02-26
	 * @used-by key()
	 * @used-by \Df\GingerPaymentsBase\Settings::api()
	 * @return string
	 */
	final protected function titleB() {return dfpm_title($this);}

	/**
	 * 2017-02-08
	 * @used-by privateKey()
	 * @used-by publicKey()
	 * @uses testable()
	 * @uses testableP()
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
	 * Cначала мы пробудем найти значение с приставкой test/live, а затем без приставки.
	 * https://english.stackexchange.com/a/200637
	 * @used-by merchantID()
	 * @used-by publicKey()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses v()
	 * @return mixed
	 */
	private function probablyTestable($k = null, $s = null, $d = null) {
		$k = $k ?: df_caller_f();
		return $this->testableGeneric($k, 'v', $s, function() use($k, $s, $d) {return $this->v($k, $s, $d);});
	}

	/**
	 * 2017-10-02
	 * @used-by privateKey()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses v()
	 * @return mixed
	 */
	private function probablyTestableP($k = null, $s = null, $d = null) {
		$k = $k ?: df_caller_f();
		return $this->testableGeneric($k, 'p', $s, function() use($k, $s, $d) {return $this->p($k, $s, $d);});
	}

	/**
	 * 2016-11-12
	 * @used-by probablyTestable()
	 * @used-by testable()
	 * @used-by testableB()
	 * @used-by testableP()
	 * @used-by testablePV()
	 * @uses \Df\Payment\Settings::p()
	 * @uses v()
	 * @param string|null $k [optional]
	 * @param string|string[] $f [optional]
	 * $f может быть массивом,
	 * и тогда первое значение его — метод для промышленного режима,
	 * а второе значение — метод для тестового режима.
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return mixed
	 */
	private function testableGeneric($k = null, $f = 'v', $s = null, $d = null) {return call_user_func(
		[$this, is_string($f) ? $f : $f[intval($this->test($s))]]
		,($this->test($s) ? 'test' : 'live') . self::phpNameToKey(ucfirst($k ?: df_caller_f()))
		,$s, $d
	);}

	/**
	 * 2017-03-27
	 * @used-by __construct()
	 * @used-by m()
	 * @used-by prefix()
	 * @used-by scopeDefault()
	 * @var M
	 */
	private $_m;
}