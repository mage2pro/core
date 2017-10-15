<?php
namespace Df\Payment;
use Df\Directory\FE\Currency as CurrencyFE;
use Df\Payment\Method as M;
use Magento\Framework\App\ScopeInterface as IScope;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;
/**
 * 2017-10-12
 * @see \Dfe\Spryng\Currency
 * @see \Dfe\Stripe\Currency
 */
class Currency {
	/**
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * 2017-02-08
	 * Конвертирует $a из учётной валюты в валюту платежа
	 * ($oq используется только для определения магазина => настроек магазина).
	 * @used-by \Df\Payment\Method::convert()
	 * @used-by \Df\Payment\Method::isAvailable()
	 * @param float $a
	 * @param O|Q $oq
	 * @return float
	 */
	final function fromBase($a, $oq) {return $this->convert($a, df_currency_base_c($oq), $oq);}

	/**
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * 2017-02-08 Converts $a from the currency of $oq to the payment currency.
	 * @used-by dfpex_from_doc()
	 * @used-by \Df\Payment\ConfigProvider::amount()
	 * @param float $a
	 * @param O|Q $oq
	 * @return float
	 */
	final function fromOrder($a, $oq) {return $this->convert($a, df_oq_currency_c($oq), $oq);}

	/**
	 * 2016-09-05 «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * Текущая валюта может меняться динамически (в том числе посетителем магазина и сессией),
	 * поэтому мы используем параметр store, а не scope.
	 * @used-by oq()
	 * @used-by rateToPayment()
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @param null|string|int|IScope|Store $s [optional]
	 * @param string|null $oc [optional]
	 * @return string
	 */
	final function iso3($s = null, $oc = null) {return dfc($this, function($s, $oc) {return
		CurrencyFE::v($this->_iso3($s), $s, $oc)
	;}, [$s, $oc]);}

	/**
	 * 2016-09-07
	 * @used-by convert()
	 * @used-by toBase()
	 * @used-by toOrder()
	 * @used-by \Df\Payment\Method::cPayment()
	 * @used-by \Df\Payment\Method::isAvailable()
	 * @used-by \Df\Payment\Operation\Source::currencyC()
	 * @param O|Q $oq
	 * @return string
	 */
	final function oq($oq) {return $this->iso3($oq->getStore(), df_oq_currency_c($oq));}

	/**
	 * 2016-09-06 Курс обмена учётной валюты на платёжную.
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @return float
	 */
	final function rateToPayment() {return df_currency_base()->getRate($this->iso3());}

	/**
	 * 2016-09-08
	 * Конвертирует $a из валюты платежа в учётную
	 * ($oq используется только для определения магазина => настроек магазина).
	 * @used-by \Df\Payment\Method::convert()
	 * @param float $a
	 * @param O|Q $oq
	 * @return float
	 */
	final function toBase($a, $oq) {return df_currency_convert($a, $this->oq($oq), df_currency_base($oq));}

	/**
	 * 2016-09-07 Конвертирует $a из валюты платежа в валюту заказа $o.
	 * @used-by \Df\Payment\Method::convert()
	 * @param float $a
	 * @param O $o
	 * @return float
	 */
	final function toOrder($a, O $o) {return df_currency_convert(
		$a, $this->oq($o), $o->getOrderCurrencyCode()
	);}

	/**
	 * 2017-10-12
	 * @used-by iso3()
	 * @see \Dfe\Spryng\Currency::_iso3()
	 * @see \Dfe\Stripe\Currency::_iso3()
	 * @param null|string|int|IScope|Store $s [optional]
	 * @return string
	 */
	protected function _iso3($s = null) {return $this->s()->v('currency', $s);}

	/**
	 * 2017-10-12
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @return Method
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2017-10-12
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by _iso3()
	 * @return Settings
	 */
	protected function s() {return $this->_m->s();}

	/**
	 * 2016-09-05
	 * Конвертирует денежную величину в валюту «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by fromBase()
	 * @used-by fromOrder()
	 * @param float $a
	 * @param string $from
	 * @param O|Q $oq
	 * @return float
	 */
	private function convert($a, $from, $oq) {return df_currency_convert($a, $from, $this->oq($oq));}

	/**
	 * 2017-10-12
	 * @used-by factory()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-10-12
	 * @used-by dfp_currency()
	 * @param object|string $m
	 * @return self
	 */
	final static function f($m) {return dfcf(function(M $m) { /** @var self $i */
		$i = df_new(df_con_heir($m, __CLASS__)); $i->_m = $m; return $i;
	}, [dfpm($m)]);}
}