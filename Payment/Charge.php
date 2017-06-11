<?php
namespace Df\Payment;
use Magento\Customer\Model\Customer as C;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-07-02
 * @see \Df\GingerPaymentsBase\Charge
 * @see \Df\PaypalClone\Charge
 * @see \Df\StripeClone\P\Charge
 * @see \Dfe\CheckoutCom\Charge
 * @see \Dfe\Square\Charge:
 * @see \Dfe\TwoCheckout\Charge
 */
abstract class Charge extends Operation {
	/**
	 * 2016-08-27
	 * @used-by customerReturnRemote()
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @param string $path [optional]
	 * @return string
	 */
	final protected function callback($path = 'confirm') {return dfp_url_callback($this->m(), $path);}

	/**
	 * 2016-08-26
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Robokassa\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Spryng\P\Charge::p()
	 * @return string
	 */
	final protected function customerIp() {return $this->o()->getRemoteIp();}

	/**
	 * 2016-08-27
	 * @return string
	 */
	final protected function customerReturn() {return dfp_url_customer_return($this->m());}

	/**
	 * 2016-08-27
	 * Этот метод решает 2 проблемы, возникающие при работе на localhost:
	 * 1) Некоторые способы оплаты (SecurePay) вообще не позволяют указывать локальные адреса.
	 * 2) Некоторые способы оплаты (allPay) допускают локальный адрес возврата,
	 * но для тестирования его нам использовать нежелательно,
	 * потому что сначала вручную сэмулировать и обработать callback.
	 *
	 * 2017-03-06
	 * Этот метод имеет преимущества перед функцией @see df_url_checkout_success(),
	 * изложенные мной 2016-07-14 для модуля allPay:
	 * 
	 * «Раньше здесь стояло df_url_checkout_success(),
	 * что показывало покупателю страницу об успешности заказа
	 * даже если покупатель не смог или не захотел оплачивать заказ.
	 *
	 * Теперь же, покупатель не смог или не захотел оплатить заказ,
	 * то при соответствующем («ReturnURL») оповещении платёжной системы
	 * мы заказ отменяем, а затем, когда платёжная система возврат покупателя в магазин,
	 * то мы проверим, не отменён ли последний заказ,
	 * и если он отменён — то восстановим корзину покупателя.»
	 * https://github.com/mage2pro/allpay/blob/1.1.31/Charge.php?ts=4#L365-L378
	 *
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 *
	 * @return string
	 */
	final protected function customerReturnRemote() {return dfp_url_customer_return_remote($this->m());}

	/**
	 * 2017-03-06
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\Robokassa\Charge::pCharge()
	 * @return string
	 */
	final protected function description() {return $this->text($this->s()->description());}

	/**
	 * 2017-03-06
	 * @used-by oiLeafs()
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @return string
	 */
	final protected function locale() {return dfc($this, function() {return
		df_locale_by_country($this->addressBS()->getCountryId())
	;});}

	/**
	 * 2016-09-07
	 * Ключами результата являются человекопонятные названия переменных.
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Dfe\Stripe\P\Charge::p()
	 * @param string|null $length [optional]
	 * @param string|null $count [optional]
	 * @return array(string => string)
	 */
	final protected function metadata($length = null, $count = null) {
		/** @var string[] $keys */
		$keys = $this->s()->metadata();
		/** @var array(string => string) $m */
		$m = array_combine(dfa_select(Metadata::s()->map(), $keys), dfa_select($this->vars(), $keys));
		return array_combine(dfa_chop(array_keys($m), $length), dfa_chop(array_values($m), $count));
	}

	/**
	 * 2016-09-07
	 * 2017-02-02
	 * Отныне метод упорядочивает позиции заказа по имени.
	 * Ведь этот метод используется только для передачи позиций заказа в платежные системы,
	 * а там они отображаются покупателю и администратору,
	 * и удобно, чтобы они были упорядочены по имени.
	 * @used-by \Dfe\CheckoutCom\Charge::setProducts()
	 * @used-by \Dfe\AllPay\Charge::productUrls()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItems()
	 * @param \Closure $f
	 * @return mixed[]
	 */
	final protected function oiLeafs(\Closure $f) {return df_oqi_leafs($this->o(), $f, $this->locale());}

	/**
	 * 2016-07-04
	 * @used-by description()
	 * @used-by \Dfe\AllPay\Charge::descriptionOnKiosk()
	 * @param string $s
	 * @return string
	 */
	final protected function text($s) {return df_var($s, $this->vars());}

	/**
	 * 2016-05-06
	 * @used-by text()
	 * @used-by metadata()
	 * @return array(string => string)
	 */
	private function vars() {return dfc($this, function() {return Metadata::vars(
		$this->store(), $this->o()
	);});}
}