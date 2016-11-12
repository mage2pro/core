<?php
namespace Df\Payment;
use Df\Payment\FormElement\Currency as CurrencyFE;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;
abstract class Settings extends \Df\Core\Settings {
	/**
	 * 2016-07-27
	 * «Ask for the Billing Address?»
	 * If checked, Magento will require the billing address.
	 * It it the default Magento behaviour.
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
	public function askForBillingAddress() {return $this->b(null, null, true);}

	/**
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * @see \Df\Payment\Settings::currency()
	 * @used-by \Df\Payment\Method::cFromBase()
	 * @param float $amount
	 * @param O $o
	 * @return float
	 */
	public function cFromBase($amount, O $o) {return $this->cConvert($amount, df_currency_base($o), $o);}

	/**
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * @used-by \Df\Payment\Method::cFromOrder()
	 * @param float $amount
	 * @param O $o
	 * @return float
	 */
	public function cFromOrder($amount, O $o) {return $this->cConvert($amount, $o->getOrderCurrency(), $o);}

	/**
	 * 2016-09-06
	 * Курс обмена учётной валюты на платёжную.
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @return float
	 */
	public function cRateToPayment() {return df_currency_base()->getRate($this->currency());}

	/**
	 * 2016-09-08
	 * Конвертирует денежную величину из валюты платежа в учётную.
	 * @param float $amount
	 * @param O $o
	 * @return float
	 */
	public function cToBase($amount, O $o) {return
		df_currency_convert($amount, $this->currencyFromO($o), df_currency_base($o))
	;}

	/**
	 * 2016-09-07
	 * Конвертирует денежную величину из валюты платежа в валюту заказа.
	 * @used-by \Dfe\TwoCheckout\Handler\RefundIssued::cm()
	 * @param float $amount
	 * @param O $o
	 * @return float
	 */
	public function cToOrder($amount, O $o) {return
		df_currency_convert($amount, $this->currencyFromO($o), $o->getOrderCurrency())
	;}

	/**
	 * 2016-09-06
	 * Код платёжной валюты.
	 * @param O $o [optional]
	 * @return string
	 */
	public function currencyC(O $o = null) {return
		df_currency_code($o ? $this->currencyFromO($o) : $this->currency())
	;}

	/**
	 * 2016-09-06
	 * Название платёжной валюты.
	 * @param Currency|string|null $oc [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string
	 */
	public function currencyN($s = null, $oc = null) {return df_currency_name($this->currency($s, $oc));}

	/**
	 * 2016-08-27
	 * @return string
	 */
	public function messageFailure() {return $this->v();}

	/**
	 * 2016-03-02
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	public function test($s = null) {return $this->b(null, $s);}
	
	/**
	 * 2016-08-25
	 * @override
	 * @see \Df\Core\Settings::prefix()
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_path_t('df_payment', dfp_method_code_short($this));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-11-12
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $default [optional]
	 * @uses v()
	 * @return mixed
	 */
	protected function testable($key = null, $s = null, $default = null) {return
		$this->testableGeneric($key ?: df_caller_f(), 'v', $s, $default)
	;}

	/**
	 * 2016-11-12
	 * @param string|null $key [optional]
	 * @param string|string[] $f [optional]
	 * $f может быть массивом,
	 * и тогда первое значение его — метод для промышленного режима,
	 * а второе значение — метод для тестового режима.
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $default [optional]
	 * @return mixed
	 */
	protected function testableGeneric($key = null, $f = 'v', $s = null, $default = null) {return
		call_user_func(
			[$this, is_string($f) ? $f : $f[intval($this->test())]]
			,($this->test() ? 'test' : 'live') . ucfirst($key ?: df_caller_f())
			,$s, $default
		)
	;}

	/**
	 * 2016-11-12
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $default [optional]
	 * @uses p()
	 * @return mixed
	 */
	protected function testableP($key = null, $s = null, $default = null) {return
		$this->testableGeneric($key ?: df_caller_f(), 'p', $s, $default)
	;}

	/**
	 * 2016-11-12
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $default [optional]
	 * @uses p()
	 * @return mixed
	 */
	protected function testablePV($key = null, $s = null, $default = null) {return
		$this->testableGeneric($key, ['p', 'v'], $s, $default)
	;}

	/**
	 * 2016-09-05
	 * Конвертирует денежную величину в валюту «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @param float $amount
	 * @param Currency|string $from
	 * @param O $o
	 * @return float
	 */
	private function cConvert($amount, $from, O $o) {return 
		df_currency_convert($amount, $from, $this->currencyFromO($o))
	;}

	/**
	 * 2016-09-05
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * Текущая валюта может меняться динамически (в том числе посетителем магазина и сессией),
	 * поэтому мы используем параметр store, а не scope
	 * @param null|string|int|S|Store $s [optional]
	 * @param Currency|string|null $oc [optional]
	 * @return Currency
	 */
	private function currency($s = null, $oc = null) {return dfc($this,
		function($s = null, $oc = null) {return
			/**
			 * 2016-09-06
			 * Здесь мы должны явно указывать ключ, потому что находиимся внутри @see \Closure,
			 * и по @see df_caller_f() в методе
			 * @see \Df\Core\Settings::v() мы получим не «currency», а «Df\Payment\{closure}».
			 *
			 * Кстати, чтобы избежать таких ошибок в дальнейшем, отныне @see df_caller_f()
			 * будет проверять, не вызывается ли она описанным образом из @see \Closure
			 */
			CurrencyFE::v($this->v('currency', $s), $s, $oc)
		;}
	, func_get_args());}
	
	/**
	 * 2016-09-07
	 * @param O $o
	 * @return Currency
	 */
	private function currencyFromO(O $o) {return $this->currency($o->getStore(), $o->getOrderCurrency());}	

	/**
	 * 2016-08-04
	 * @param object|string $class
	 * @param string $key [optional]
	 * @param null|string|int|S $scope [optional]
	 * @param mixed|callable $default [optional]
	 * @return self
	 */
	public static function convention($class, $key = '', $scope = null, $default = null) {
		/** array(string => self) $cache */
		static $cache;
		/** @var string $key */
		$cacheKey = df_module_name($class);
		if (!isset($cache[$cacheKey])) {
			$cache[$cacheKey] = self::s(df_con($class, 'Settings'));
		}
		/** @var self $result */
		$result = $cache[$cacheKey];
		return df_null_or_empty_string($key) ? $result : $result->v($key, $scope, $default);
	}
}