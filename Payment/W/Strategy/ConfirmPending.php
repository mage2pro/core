<?php
namespace Df\Payment\W\Strategy;
use Df\Payment\Source\AC;
use Df\Payment\W\Event as Ev;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-15
 * @used-by \Df\GingerPaymentsBase\W\Handler::strategyC()
 * @used-by \Df\PaypalClone\W\Handler::strategyC()
 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
 * @used-by \Dfe\Qiwi\W\Handler::strategyC()
 */
final class ConfirmPending extends \Df\Payment\W\Strategy {
	/**
	 * 2017-01-15
	 * @override
	 * @see \Df\Payment\W\Strategy::_handle()
	 * @used-by \Df\Payment\W\Strategy::::handle()
	 */
	protected function _handle() {
		$o = $this->o(); /** @var O $o */
		/**
		 * 2016-03-15
		 * Если оставить открытой транзакцию «capture»,
		 * то операция «void» (отмена авторизации платежа) будет недоступна:
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L540-L555
		 * @used-by \Magento\Sales\Model\Order\Payment::canVoid()
		 * Транзакция считается закрытой, если явно не указать «false».
		 *
		 * 2017-01-16
		 * Наоборот: если закрыть транзакцию типа «authorize»,
		 * то операция «Capture Online» из административного интерфейса будет недоступна:
		 * @see \Magento\Sales\Model\Order\Payment::canCapture()
		 *		if ($authTransaction && $authTransaction->getIsClosed()) {
		 *			$orderTransaction = $this->transactionRepository->getByTransactionType(
		 *				Transaction::TYPE_ORDER,
		 *				$this->getId(),
		 *				$this->getOrder()->getId()
		 *			);
		 *			if (!$orderTransaction) {
		 *				return false;
		 *			}
		 *		}
		 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment.php#L263-L281
		 * «How is \Magento\Sales\Model\Order\Payment::canCapture() implemented and used?»
		 * https://mage2.pro/t/650
		 * «How does Magento 2 decide whether to show the «Capture Online» dropdown
		 * on a backend's invoice screen?»: https://mage2.pro/t/2475
		 */
		$op = $this->op(); /** @var OP $op */
		// 2017-03-29
		// Сегодня заметил, что Kassa Compleet долбится несколько раз для одного и того же платежа.
		// Это приводило к повторному созданию invoice (второй invoice был с нулевой суммой).
		if (!$o->getTotalDue()) {
			$this->softFailure('This payment is already confirmed.');
		}
		else {
			$e = $this->e(); /** @var Ev $e */
			// 2016-07-14
			// Если покупатель не смог или не захотел оплатить заказ, то мы заказ отменяем,
			// а затем, когда платёжная система возвратит покупателя в магазин,
			// то мы проверим, не отменён ли последний заказ,
			// и если он отменён — то восстановим корзину покупателя.
			$succ = $e->isSuccessful(); /** @var bool $succ */
			/**
			 * 2017-08-30
			 * If you want to ignore an event here, then:
			 * 1) Return `true` from @see \Df\Payment\W\Event::isSuccessful()
			 * 2) Return any value except \Df\Payment\W\Event::T_AUTHORIZE and \Df\Payment\W\Event::T_CAPTURE
			 * from @see \Df\Payment\W\Event::ttCurrent().
			 * This value will be the current transaction suffix:
			 * @used-by \Df\PaypalClone\W\Nav::id()
			 * @used-by \Df\StripeClone\W\Nav::id()
			 * so it should be unique in a payment processing cycle:
			 * a particular payment can not have multiple transactions with the same suffix.
			 */
			if ($succ && ($action = dfa([Ev::T_AUTHORIZE => AC::A, Ev::T_CAPTURE => AC::C], $e->ttCurrent()))) {
				/** @var string|null $action */
				$op->setIsTransactionClosed(AC::C === $action);
				/**
				 * 2017-01-15
				 * $this->m()->setStore($o->getStoreId()); здесь не нужно,
				 * потому что это делается автоматически в ядре:
				 * @see \Magento\Sales\Model\Order\Payment\Operations\AuthorizeOperation::authorize():
				 * 		$method->setStore($order->getStoreId());
				 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment/Operations/AuthorizeOperation.php#L44
				 *
				 * 2017-03-26
				 * Этот вызов приводит к добавлению транзакции типа $action:
				 * https://github.com/mage2pro/core/blob/2.4.2/Payment/W/Nav.php#L100-L114
				 * Идентификатор и данные транзакции мы уже установили в методе @see \Df\Payment\W\Nav::op()
				 */
				dfp_action($op, $action);
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
				$op->addTransaction(T::TYPE_PAYMENT);
				if (!$succ) {
					$o->cancel();
				}
			}
			$o->save();
			// 2016-08-17
			// https://code.dmitry-fedyuk.com/m2e/allpay/issues/17
			// Письмо отсылаем только если isSuccessful() вернуло true
			// (при этом не факт, что оплата уже прошла: при оффлайновом способе оплаты
			// isSuccessful() говорит лишь о том, что покупатель успешно выбрал оффлайновый способ оплаты,
			// а подтверждение платежа придёт лишь потом, через несколько дней).
			if ($succ) {
				dfp_mail($o);
			}
			// 2017-09-13
			// We do not set a response here, because PayPal clones require a specific response on success.
		}
	}
}