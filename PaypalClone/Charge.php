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
 * @see \Dfe\PostFinance\Charge
 * @see \Dfe\Robokassa\Charge
 * @see \Dfe\SecurePay\Charge
 * @see \Dfe\YandexKassa\Charge
 */
abstract class Charge extends \Df\Payment\Charge {
	/**
	 * 2017-08-19
	 * @used-by self::p()
	 * @see \Dfe\AllPay\Charge::k_Amount()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Amount()
	 * @see \Dfe\Dragonpay\Charge::k_Amount()
	 * @see \Dfe\IPay88\Charge::k_Amount()
	 * @see \Dfe\PostFinance\Charge::k_Amount()
	 * @see \Dfe\Robokassa\Charge::k_Amount()
	 * @see \Dfe\SecurePay\Charge::k_Amount()
	 * @see \Dfe\YandexKassa\Charge::k_Amount()
	 */
	abstract protected function k_Amount():string;

	/**
	 * 2017-08-19
	 * @used-by self::p()
	 * @see \Dfe\AllPay\Charge::k_MerchantId()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_MerchantId()
	 * @see \Dfe\Dragonpay\Charge::k_MerchantId()
	 * @see \Dfe\IPay88\Charge::k_MerchantId()
	 * @see \Dfe\PostFinance\Charge::k_MerchantId()
	 * @see \Dfe\Robokassa\Charge::k_MerchantId()
	 * @see \Dfe\SecurePay\Charge::k_MerchantId()
	 * @see \Dfe\YandexKassa\Charge::k_MerchantId()
	 */
	abstract protected function k_MerchantId():string;

	/**
	 * 2016-08-29
	 * @used-by self::p()
	 * @see \Dfe\AllPay\Charge::k_RequestId()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_RequestId()
	 * @see \Dfe\Dragonpay\Charge::k_RequestId()
	 * @see \Dfe\IPay88\Charge::k_RequestId()
	 * @see \Dfe\PostFinance\Charge::k_RequestId()
	 * @see \Dfe\Robokassa\Charge::k_RequestId()
	 * @see \Dfe\SecurePay\Charge::k_RequestId()
	 * @see \Dfe\YandexKassa\Charge::k_RequestId()
	 */
	abstract protected function k_RequestId():string;

	/**
	 * 2016-08-27
	 * 2017-09-25
	 * The method can return an empty string if the request does not need a signature.
	 * Currently, only the Yandex.Kassa charge requests do not use a signature:
	 * @see \Dfe\YandexKassa\Charge::k_Signature()
	 * https://tech.yandex.com/money/doc/payment-solution/payment-form/payment-form-http-docpage
	 * 2022-11-10 @see \Dfe\AlphaCommerceHub\Charge::k_Signature() returns '' too.
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::k_Signature()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Signature()
	 * @see \Dfe\Dragonpay\Charge::k_Signature()
	 * @see \Dfe\IPay88\Charge::k_Signature()
	 * @see \Dfe\PostFinance\Charge::k_Signature()
	 * @see \Dfe\Robokassa\Charge::k_Signature()
	 * @see \Dfe\SecurePay\Charge::k_Signature()
	 * @see \Dfe\YandexKassa\Charge::k_Signature()
	 */
	abstract protected function k_Signature():string;

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::pCharge()
	 * @see \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @see \Dfe\Dragonpay\Charge::pCharge()
	 * @see \Dfe\IPay88\Charge::pCharge()
	 * @see \Dfe\PostFinance\Charge::pCharge()
	 * @see \Dfe\Robokassa\Charge::pCharge()
	 * @see \Dfe\SecurePay\Charge::pCharge()
	 * @see \Dfe\YandexKassa\Charge::pCharge()
	 * @return array(string => mixed)
	 */
	abstract protected function pCharge():array;

	/**
	 * 2017-01-05
	 * Локальный внутренний идентификатор транзакции.
	 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
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
	 */
	final protected function id():string {return Identification::get($this->o());}

	/**
	 * 2017-08-19
	 * @used-by self::p()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Currency()
	 * @see \Dfe\Dragonpay\Charge::k_Currency()
	 * @see \Dfe\IPay88\Charge::k_Currency()
	 * @see \Dfe\PostFinance\Charge::k_Currency()
	 * @see \Dfe\SecurePay\Charge::k_Currency()
	 */
	protected function k_Currency():string {return '';}

	/**
	 * 2017-08-19
	 * @used-by self::p()
	 * @see \Dfe\AlphaCommerceHub\Charge::k_Email()
	 * @see \Dfe\Dragonpay\Charge::k_Email()
	 * @see \Dfe\IPay88\Charge::k_Email()
	 * @see \Dfe\PostFinance\Charge::k_Email()
	 * @see \Dfe\Robokassa\Charge::k_Email()
	 * @see \Dfe\SecurePay\Charge::k_Email()
	 * @see \Dfe\YandexKassa\Charge::k_Email()
	 */
	protected function k_Email():string {return '';}

	/**
	 * 2017-08-19
	 * @used-by self::p()
	 * @see \Dfe\IPay88\Charge::testAmountF()
	 * @return float|int|string
	 */
	protected function testAmountF() {return $this->amountF();}

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Init\Action::charge()
	 * @return array(string, array(string => mixed))
	 */
	final static function p(Method $m):array {
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