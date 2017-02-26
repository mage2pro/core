<?php
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Df\Directory\FormElement\Currency as CurrencyFE;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Sales\Model\Order as O;
use Magento\Quote\Model\Quote as Q;
use Magento\Store\Model\Store;
/**
 * 2017-02-15
 * @method static Settings conventionB(string|object $c)
 * @see \Df\GingerPaymentsBase\Settings
 * @see \Df\Payment\Settings\BankCard
 * @see \Dfe\AllPay\Settings
 * @see \Dfe\Klarna\Settings
 */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * 2017-02-08
	 * Конвертирует $amount из учётной валюты в валюту платежа
	 * ($oq используется только для определения магазина => настроек магазина).
	 * @see _cur()
	 * @used-by \Df\Payment\Method::cFromBase()
	 * @param float $amount
	 * @param O|Q $oq
	 * @return float
	 */
	final function cFromBase($amount, $oq) {return
		$this->cConvert($amount, df_currency_base($oq), $oq)
	;}

	/**
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * 2017-02-08
	 * Конвертирует $amount из валюты заказа $oq в валюту платежа.
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\Method::cFromOrder()
	 * @param float $amount
	 * @param O|Q $oq
	 * @return float
	 */
	final function cFromOrder($amount, $oq) {return
		$this->cConvert($amount, df_oq_currency($oq), $oq)
	;}

	/**
	 * 2016-09-06
	 * Курс обмена учётной валюты на платёжную.
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @return float
	 */
	final function cRateToPayment() {return df_currency_base()->getRate($this->_cur());}

	/**
	 * 2016-09-08
	 * Конвертирует $amount из валюты платежа в учётную
	 * ($oq используется только для определения магазина => настроек магазина).
	 * @param float $amount
	 * @param O $o
	 * @return float
	 */
	final function cToBase($amount, O $o) {return
		df_currency_convert($amount, $this->currencyFromOQ($o), df_currency_base($o))
	;}

	/**
	 * 2016-09-07
	 * Конвертирует $amount из валюты платежа в валюту заказа $o.
	 * @used-by \Dfe\TwoCheckout\Handler\RefundIssued::cm()
	 * @param float $amount
	 * @param O $o
	 * @return float
	 */
	final function cToOrder($amount, O $o) {return
		df_currency_convert($amount, $this->currencyFromOQ($o), $o->getOrderCurrency())
	;}

	/**
	 * 2016-09-06
	 * Код платёжной валюты.
	 * @param O|Q $oq [optional]
	 * @return string
	 */
	final function currencyC($oq = null) {return
		df_currency_code($oq ? $this->currencyFromOQ($oq) : $this->_cur())
	;}

	/**
	 * 2016-09-06
	 * Название платёжной валюты.
	 * @param Currency|string|null $oc [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string
	 */
	final function currencyN($s = null, $oc = null) {return
		df_currency_name($this->_cur($s, $oc))
	;}

	/**
	 * 2016-11-16
	 * «Description»
	 * @used-by \Df\StripeClone\Charge::request()
	 * @return string
	 */
	final function description() {return $this->v();}

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
	 * @used-by \Df\Payment\Method::action()
	 * @see \Dfe\TwoCheckout\Settings::init()
	 * @see \Dfe\Omise\Settings::init()
	 * @see \Dfe\Stripe\Settings::init()
	 * @return void
	 */
	function init() {}

	/**
	 * 2016-12-26
	 * @return bool
	 */
	final function log() {return $this->b(null, null, true);}

	/**
	 * 2016-08-27
	 * @return string
	 */
	final function messageFailure() {return $this->v();}

	/**
	 * 2017-02-08
	 * @used-by \Df\GingerPaymentsBase\Settings::api()
	 * @used-by \Dfe\CheckoutCom\Settings::api()
	 * @used-by \Dfe\Omise\Settings::init()
	 * @used-by \Dfe\Paymill\T\Charge::t01()
	 * @used-by \Dfe\Spryng\Settings::api()
	 * @used-by \Dfe\Stripe\Settings::init()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @param null|string|int|S|Store $s [optional]
	 * @return string
	 */
	final function privateKey($s = null) {return $this->key('testableP', 'private', 'secret', $s);}

	/**
	 * 2016-11-12
	 * @see \Dfe\Square\Settings::publicKey()
	 * @return string
	 */
	function publicKey() {return $this->key('testable', 'public', 'publishable');}

	/**
	 * 2016-07-27
	 * «Require the billing address?»
	 * If checked, Magento will require the billing address.
	 * It is the default Magento behaviour.
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
	 * @return bool
	 */
	final function requireBillingAddress() {return $this->b(null, null, function() {return
		// 2017-02-16
		// https://github.com/mage2pro/core/issues/8
		$this->b('askForBillingAddress', null, true)
	;});}

	/**
	 * 2016-03-02
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	final function test($s = null) {return $this->b(null, $s);}
	
	/**
	 * 2016-08-25
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	final protected function prefix() {return dfc($this, function() {return
		'df_payment/' . dfp_method_code_short($this)
	;});}

	/**
	 * 2016-11-12
	 * @param string|null $k [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses v()
	 * @return mixed
	 */
	final protected function testable($k = null, $s = null, $d = null) {return
		$this->testableGeneric($k ?: df_caller_f(), 'v', $s, $d)
	;}

	/**
	 * 2016-12-24
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses b()
	 * @return bool
	 */
	final protected function testableB($key = null, $s = null, $d = null) {return
		$this->testableGeneric($key ?: df_caller_f(), 'b', $s, $d)
	;}

	/**
	 * 2016-11-12
	 * @uses \Df\Payment\Settings::p()
	 * @uses v()
	 * @param string|null $key [optional]
	 * @param string|string[] $f [optional]
	 * $f может быть массивом,
	 * и тогда первое значение его — метод для промышленного режима,
	 * а второе значение — метод для тестового режима.
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return mixed
	 */
	final protected function testableGeneric($key = null, $f = 'v', $s = null, $d = null) {return
		call_user_func(
			[$this, is_string($f) ? $f : $f[intval($this->test())]]
			,($this->test() ? 'test' : 'live') . self::phpNameToKey(ucfirst($key ?: df_caller_f()))
			,$s, $d
		)
	;}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае,
	 * когда значение шифруется как в промышленном, так и в тестовом режимах.
	 * @used-by \Df\StripeClone\Settings::privateKey()
	 * @used-by \Dfe\Klarna\Settings::sharedSecret()
	 * @used-by \Dfe\Square\Settings::accessToken()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @used-by \Dfe\TwoCheckout\Settings::secretWord()
	 * Если значение шифруется только в промышленном режиме, то используйте @see testablePV()
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses \Df\Payment\Settings::p()
	 * @return mixed
	 */
	final protected function testableP($key = null, $s = null, $d = null) {return
		$this->testableGeneric($key ?: df_caller_f(), 'p', $s, $d)
	;}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае,
	 * когда значение шифруется в промышленном режиме, но не шифруется в тестовом.
	 * @used-by \Dfe\AllPay\Settings::hashIV()
	 * @used-by \Dfe\AllPay\Settings::hashKey()
	 * @used-by \Dfe\SecurePay\Settings::transactionPassword()
	 * Если значение шифруется в обоих режимах, то используйте @see testableP()
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses p()
	 * @return mixed
	 */
	final protected function testablePV($key = null, $s = null, $d = null) {return
		$this->testableGeneric($key ?: df_caller_f(), ['p', 'v'], $s, $d)
	;}

	/**
	 * 2016-09-05
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * Текущая валюта может меняться динамически (в том числе посетителем магазина и сессией),
	 * поэтому мы используем параметр store, а не scope.
	 * @used-by cRateToPayment()
	 * @used-by currencyC()
	 * @used-by currencyFromOQ()
	 * @used-by currencyN()
	 * @param null|string|int|S|Store $s [optional]
	 * @param Currency|string|null $oc [optional]
	 * @return Currency
	 */
	private function _cur($s = null, $oc = null) {return dfc($this,
		function($s = null, $oc = null) {return CurrencyFE::v($this->currency($s), $s, $oc);}
	,func_get_args());}

	/**
	 * 2017-01-25
	 * @used-by _cur()
	 * @see \Dfe\Klarna\Settings::currency()
	 * @see \Dfe\Spryng\Settings::currency()
	 * @param null|string|int|S|Store $s [optional]
	 * @return string
	 */
	protected function currency($s = null) {return $this->v(null, $s);}

	/**
	 * 2016-09-05
	 * Конвертирует денежную величину в валюту «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @param float $amount
	 * @param Currency|string $from
	 * @param O|Q $oq
	 * @return float
	 */
	private function cConvert($amount, $from, $oq) {return
		df_currency_convert($amount, $from, $this->currencyFromOQ($oq))
	;}

	/**
	 * 2017-02-26
	 * @used-by key()
	 * @used-by \Df\GingerPaymentsBase\Settings::api()
	 * @return string
	 */
	final protected function titleB() {return dfp_method_title($this);}
	
	/**
	 * 2016-09-07
	 * @param O|Q $oq [optional]
	 * @return Currency
	 */
	private function currencyFromOQ($oq) {return $this->_cur($oq->getStore(), df_oq_currency($oq));}

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
	 * @return string
	 * @throws DFE
	 */
	private function key($method, $type, $alt, $s = null) {return
		$this->$method("{$type}Key", $s, function() use($method, $alt, $s) {return
			$this->$method("{$alt}Key", $s);}
		) ?: df_error("Please set your {$this->titleB()} $type key in the Magento backend.")
	;}
}