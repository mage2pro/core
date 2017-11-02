<?php
namespace Df\PaypalClone;
use Df\Payment\Settings;
use Df\Payment\Source\Identification;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-08-27
 * @see \Dfe\AllPay\Charge
 * @see \Dfe\AlphaCommerceHub\Charge
 * @see \Dfe\Dragonpay\Charge
 * @see \Dfe\IPay88\Charge
 * @see \Dfe\MPay24\Charge
 * @see \Dfe\Paypal\Charge
 * @see \Dfe\Paystation\Charge
 * @see \Dfe\PostFinance\Charge
 * @see \Dfe\Robokassa\Charge
 * @see \Dfe\SecurePay\Charge
 * @see \Dfe\Tinkoff\Charge
 * @see \Dfe\YandexKassa\Charge
 */
abstract class Charge extends \Df\Payment\Charge {
	/**
	 * 2017-08-19
	 * @used-by p()
	 * @see \Dfe\AllPay\Charge::k_Amount()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Amount()
	 * @see \Dfe\Dragonpay\Charge::k_Amount()
	 * @see \Dfe\IPay88\Charge::k_Amount()
	 * @see \Dfe\MPay24\Charge::k_Amount()
	 * @see \Dfe\Paypal\Charge::k_Amount()
	 * @see \Dfe\Paystation\Charge::k_Amount()
	 * @see \Dfe\PostFinance\Charge::k_Amount()
	 * @see \Dfe\Robokassa\Charge::k_Amount()
	 * @see \Dfe\SecurePay\Charge::k_Amount()
	 * @see \Dfe\Tinkoff\Charge::k_Amount()
	 * @see \Dfe\YandexKassa\Charge::k_Amount()
	 * @return string
	 */
	abstract protected function k_Amount();

	/**
	 * 2017-08-19
	 * @used-by p()
	 * @see \Dfe\AllPay\Charge::k_MerchantId()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_MerchantId()
	 * @see \Dfe\Dragonpay\Charge::k_MerchantId()
	 * @see \Dfe\IPay88\Charge::k_MerchantId()
	 * @see \Dfe\MPay24\Charge::k_MerchantId()
	 * @see \Dfe\Paypal\Charge::k_MerchantId()
	 * @see \Dfe\Paystation\Charge::k_MerchantId()
	 * @see \Dfe\PostFinance\Charge::k_MerchantId()
	 * @see \Dfe\Robokassa\Charge::k_MerchantId()
	 * @see \Dfe\SecurePay\Charge::k_MerchantId()
	 * @see \Dfe\Tinkoff\Charge::k_MerchantId()
	 * @see \Dfe\YandexKassa\Charge::k_MerchantId()
	 * @return string
	 */
	abstract protected function k_MerchantId();

	/**
	 * 2016-08-29
	 * @used-by p()
	 * @see \Dfe\AllPay\Charge::k_RequestId()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_RequestId()
	 * @see \Dfe\Dragonpay\Charge::k_RequestId()
	 * @see \Dfe\IPay88\Charge::k_RequestId()
	 * @see \Dfe\MPay24\Charge::k_RequestId()
	 * @see \Dfe\Paypal\Charge::k_RequestId()
	 * @see \Dfe\Paystation\Charge::k_RequestId()
	 * @see \Dfe\PostFinance\Charge::k_RequestId()
	 * @see \Dfe\Robokassa\Charge::k_RequestId()
	 * @see \Dfe\SecurePay\Charge::k_RequestId()
	 * @see \Dfe\Tinkoff\Charge::k_RequestId()
	 * @see \Dfe\YandexKassa\Charge::k_RequestId()
	 * @return string
	 */
	abstract protected function k_RequestId();

	/**
	 * 2016-08-27
	 * 2017-09-25
	 * The method can return null if the request does not need a signature.
	 * Currently, only the Yandex.Kassa charge requests do not use a signature:
	 * @see \Dfe\YandexKassa\Charge::k_Signature()
	 * https://tech.yandex.com/money/doc/payment-solution/payment-form/payment-form-http-docpage
	 *
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::k_Signature()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Signature()
	 * @see \Dfe\Dragonpay\Charge::k_Signature()
	 * @see \Dfe\IPay88\Charge::k_Signature()
	 * @see \Dfe\MPay24\Charge::k_Signature()
	 * @see \Dfe\Paypal\Charge::k_Signature()
	 * @see \Dfe\Paystation\Charge::k_Signature()
	 * @see \Dfe\PostFinance\Charge::k_Signature()
	 * @see \Dfe\Robokassa\Charge::k_Signature()
	 * @see \Dfe\SecurePay\Charge::k_Signature()
	 * @see \Dfe\Tinkoff\Charge::k_Signature()
	 * @see \Dfe\YandexKassa\Charge::k_Signature()
	 * @return string|null
	 */
	abstract protected function k_Signature();

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::pCharge()
	 * @see \Dfe\Dragonpay\Charge::pCharge()
	 * @see \Dfe\IPay88\Charge::pCharge()
	 * @see \Dfe\MPay24\Charge::pCharge()
	 * @see \Dfe\Paypal\Charge::pCharge()
	 * @see \Dfe\Paystation\Charge::pCharge()
	 * @see \Dfe\PostFinance\Charge::pCharge()
	 * @see \Dfe\Robokassa\Charge::pCharge()
	 * @see \Dfe\SecurePay\Charge::pCharge()
	 * @see \Dfe\Tinkoff\Charge::pCharge()
	 * @see \Dfe\YandexKassa\Charge::pCharge()
	 * @return array(string => mixed)
	 */
	abstract protected function pCharge();

	/**
	 * 2017-01-05
	 * Локальный внутренний идентификатор транзакции.
	 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 *
	 * 2017-04-16
	 * Прошлые комментарии для модуля allPay:
	 * ======================================
	 * 2016-08-29
	 * 2016-07-02
	 * «Merchant trade number».
	 * Varchar(20)
	 * «Merchant trade number could not be repeated.
	 * It is composed with upper and lower cases of English letter and numbers.»
	 * Must be filled.
	 *
	 * 2016-07-05
	 * Значение может содержать только цифры и латинские буквы.
	 * Все другие символы недопустимы.
	 * В принципе, стандартные номера заказов удовлетворяют этим условиям,
	 * но вот нестандартные, вида ORD-2016/07-00274
	 * (которые делает наш модуль Sales Documents Numberation) — не удовлетворяют.
	 * Поэтому надо перекодировать проблемные символы.
	 *
	 * Второй мыслью было использовать df_encryptor()->encrypt($this->o()->getIncrementId())
	 * Однако хэш md5 имеет длину 32 символа: http://stackoverflow.com/questions/6317276
	 * А хэш sha256 — 64 символа: http://stackoverflow.com/questions/3064133
	 * allPay же ограничивает длину идентификатора 20 символами.
	 *
	 * Поэтому используем иное решение: нестандартный идентификатор транзакции.
	 *
	 * 2016-07-17
	 * Клиент просит, чтобы в качестве идентификатора платежа
	 * всё-таки использовался номер заказа:
	 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/7
	 * В принципе, это разумно: ведь нестандартные номера заказов
	 * (которые, например, делает наш модуль Sales Documents Numberation)
	 * будут использовать лишь немногие клиенты,
	 * большинство же будет использовать стандартные номера заказов,
	 * поэтому разумно предоставить этому большинству возможность
	 * использовать в качестве идентификатора платежа номер заказа.
	 * ======================================
	 *
	 * 2017-09-04 Our local (without the module prefix) internal payment ID.
	 *
	 * @override
	 * @see \Df\Payment\Operation::id()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @return string
	 */
	final protected function id() {return Identification::get($this->o());}

	/**
	 * 2017-08-19
	 * @used-by p()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Currency()
	 * @see \Dfe\Dragonpay\Charge::k_RequestId()
	 * @see \Dfe\IPay88\Charge::k_RequestId()
	 * @see \Dfe\PostFinance\Charge::k_RequestId()
	 * @see \Dfe\SecurePay\Charge::k_RequestId()
	 * @return string|null
	 */
	protected function k_Currency() {return null;}

	/**
	 * 2017-08-19
	 * @used-by p()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Email()
	 * @see \Dfe\Dragonpay\Charge::k_Email()
	 * @see \Dfe\IPay88\Charge::k_Email()
	 * @see \Dfe\PostFinance\Charge::k_Email()
	 * @see \Dfe\Robokassa\Charge::k_Email()
	 * @see \Dfe\SecurePay\Charge::k_Email()
	 * @see \Dfe\YandexKassa\Charge::k_Email()
	 * @return string|null
	 */
	protected function k_Email() {return null;}

	/**
	 * 2017-08-19
	 * @used-by p()
	 * @see \Dfe\IPay88\Charge::testAmountF()
	 * @return float|int|string
	 */
	protected function testAmountF() {return $this->amountF();}

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Init\Action::charge()
	 * @param Method $m
	 * @return array(string, array(string => mixed))
	 */
	final static function p(Method $m) {
		$i = df_new(df_con_heir($m, __CLASS__), $m); /** @var self $i */
		/**
		 * 2017-01-05
		 * @uses \Df\Payment\Operation::id()
		 * @uses \Dfe\AllPay\Charge::id()
		 * Локальный внутренний идентификатор транзакции.
		 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
		 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
		 * ведь там все идентификаторы имели бы одинаковую приставку.
		 */
		$id = df_assert_sne($i->id()); /** @var string $id */
		$s = $i->s(); /** @var Settings $s */
		$p = df_clean_keys([
			$i->k_Amount() => $s->test() ? $i->testAmountF() : $i->amountF()
			,$i->k_Currency() => $i->currencyC()
			,$i->k_Email() => $i->customerEmail()
			,$i->k_MerchantId() => $s->merchantID()
			,$i->k_RequestId() => $id
	 	]) + $i->pCharge();  /** @var array(string => mixed) $p */
		/**
		 * 2017-09-25
		 * The Yandex.Kassa charge requests do not use a signature:
		 * @see \Dfe\YandexKassa\Charge::k_Signature()
		 * https://tech.yandex.com/money/doc/payment-solution/payment-form/payment-form-http-docpage
		 */
		return [$id, $p + ((!$kS = $i->k_Signature()) ? [] : [$kS => Signer::signRequest($i, $p)])];
	}
}