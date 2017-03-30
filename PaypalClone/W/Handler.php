<?php
namespace Df\PaypalClone\W;
use Df\Payment\Source\AC;
use Df\PaypalClone\Signer;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-07-12
 * @see \Dfe\AllPay\W\Handler
 * 2017-03-20
 * The class is not abstract anymore: you can use it as a base for a virtual type:
 * 1) SecurePay: https://github.com/mage2pro/securepay/blob/1.4.2/etc/di.xml#L8
 * @method Event e()
 */
class Handler extends \Df\Payment\W\Handler {
	/**
	 * 2017-01-01
	 * @override
	 * @see \Df\Payment\W\Handler::_handle()
	 * @used-by \Df\Payment\W\Handler::handle()
	 */
	final protected function _handle() {
		/** @var Event $e */
		$e = $this->e();
		// 2016-07-14
		// Если покупатель не смог или не захотел оплатить заказ, то мы заказ отменяем,
		// а затем, когда платёжная система возвратит покупателя в магазин,
		// то мы проверим, не отменён ли последний заказ,
		// и если он отменён — то восстановим корзину покупателя.
		/** @var bool $succ */
		if (($succ = $e->isSuccessful()) && $e->needCapture()) {
			/**
			 * 2017-03-26
			 * Этот вызов приводит к добавлению транзакции типа @see AC::C:
			 * https://github.com/mage2pro/core/blob/2.4.2/Payment/W/Nav.php#L100-L114
			 * Идентификатор и данные транзакции мы уже установили в методе @see \Df\Payment\W\Nav::op()
			 */
			dfp_action($this->op(), AC::C);
		}
		else {
			/**
			 * 2016-07-10
			 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
			 * это единственная транзакции без специального назначения,
			 * и поэтому мы можем безопасно его использовать.
			 * 2017-01-16
			 * Идентификатор и данные транзакции мы уже установили в методе @see \Df\Payment\W\Nav::op()
			 */
			$this->op()->addTransaction(T::TYPE_PAYMENT);
			if (!$succ) {
				$this->o()->cancel();
			}
		}
		$this->o()->save();
		// 2016-08-17
		// https://code.dmitry-fedyuk.com/m2e/allpay/issues/17
		// Письмо отсылаем только если isSuccessful() вернуло true
		// (при этом не факт, что оплата уже прошла: при оффлайновом способе оплаты
		// isSuccessful() говорит лишь о том, что покупатель успешно выбрал оффлайновый способ оплаты,
		// а подтверждение платежа придёт лишь потом, через несколько дней).
		if ($succ) {
			dfp_mail($this->o());
		}
	}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @override
	 * @see \Df\Payment\W\Handler::validate()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @throws \Exception
	 */
	final protected function validate() {
		/** @var string $e */
		/** @var string $p */
		if (($e = Signer::signResponse($this, $this->r())) !== ($p = $this->e()->signatureProvided())) {
			df_error("Invalid signature.\nExpected: «{$e}».\nProvided: «{$p}».");
		}
	}
}