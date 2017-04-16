<?php
namespace Df\PaypalClone;
use Df\PaypalClone\Source\Identification;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-08-27
 * @see \Dfe\AllPay\Charge
 * @see \Dfe\Dragonpay\Charge
 * @see \Dfe\IPay88\Charge
 * @see \Dfe\MPay24\Charge
 * @see \Dfe\Paystation\Charge
 * @see \Dfe\Robokassa\Charge
 * @see \Dfe\SecurePay\Charge
 * @see \Dfe\YandexKassa\Charge
 */
abstract class Charge extends \Df\Payment\Charge {
	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::pCharge()
	 * @see \Dfe\Dragonpay\Charge::pCharge()
	 * @see \Dfe\IPay88\Charge::pCharge()
	 * @see \Dfe\MPay24\Charge::pCharge()
	 * @see \Dfe\Paystation\Charge::pCharge()
	 * @see \Dfe\Robokassa\Charge::pCharge()
	 * @see \Dfe\SecurePay\Charge::pCharge()
	 * @see \Dfe\YandexKassa\Charge::pCharge()
	 * @return array(string => mixed)
	 */
	abstract protected function pCharge();

	/**
	 * 2016-08-29
	 * @used-by p()
	 * @see \Dfe\AllPay\Charge::k_RequestId()
	 * @see \Dfe\Dragonpay\Charge::k_RequestId()
	 * @see \Dfe\IPay88\Charge::k_RequestId()
	 * @see \Dfe\MPay24\Charge::k_RequestId()
	 * @see \Dfe\Paystation\Charge::k_RequestId()
	 * @see \Dfe\Robokassa\Charge::k_RequestId()
	 * @see \Dfe\SecurePay\Charge::k_RequestId()
	 * @see \Dfe\YandexKassa\Charge::k_RequestId()
	 * @return string
	 */
	abstract protected function k_RequestId();

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::k_Signature()
	 * @see \Dfe\Dragonpay\Charge::k_Signature()
	 * @see \Dfe\IPay88\Charge::k_Signature()
	 * @see \Dfe\MPay24\Charge::k_Signature()
	 * @see \Dfe\Paystation\Charge::k_Signature()
	 * @see \Dfe\Robokassa\Charge::k_Signature()
	 * @see \Dfe\SecurePay\Charge::k_Signature()
	 * @see \Dfe\YandexKassa\Charge::k_Signature()
	 * @return string
	 */
	abstract protected function k_Signature();

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
	 * @override
	 * @see \Df\Payment\Operation::id()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @return string
	 */
	final protected function id() {return Identification::get($this->o());}

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Method::getConfigPaymentAction()
	 * @param Method $m
	 * @return array(string, array(string => mixed))
	 */
	final static function p(Method $m) {
		/** @var self $i */
		$i = df_new(df_con_heir($m, __CLASS__), $m);
		/**
		 * 2017-01-05
		 * @uses \Df\Payment\Operation::id()
		 * @uses \Dfe\AllPay\Charge::id()
		 * Локальный внутренний идентификатор транзакции.
		 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
		 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
		 * ведь там все идентификаторы имели бы одинаковую приставку.
		 */
		/** @var string $id */
		$id = df_assert_sne($i->id());
		/** @var array(string => mixed) $p */
		$p = [$i->k_RequestId() => $id] + $i->pCharge();
		return [$id, $p + [$i->k_Signature() => Signer::signRequest($i, $p)]];
	}
}