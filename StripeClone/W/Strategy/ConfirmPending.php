<?php
namespace Df\StripeClone\W\Strategy;
use Df\Payment\Source\AC;
use Df\StripeClone\W\Event as Ev;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-01-15
/** @used-by \Dfe\Omise\W\Handler\Charge\Complete::strategyC() */
final class ConfirmPending extends \Df\StripeClone\W\Strategy {
	/**
	 * 2017-01-15
	 * @override
	 * @see \Df\StripeClone\W\Strategy::_handle()
	 * @used-by \Df\StripeClone\W\Strategy::::handle()
	 */
	protected function _handle() {
		$o = $this->o(); /** @var O $o */
		// 2017-03-29
		// Сегодня заметил, что Kassa Compleet долбится несколько раз для одного и того же платежа.
		// Это приводило к повторному созданию invoice (второй invoice был с нулевой суммой).
		if (!$o->getTotalDue()) {
			$this->resultSet('This payment is already confirmed.');
		}
		else {
			$this->action();
			dfp_mail($o);
			$this->resultSet($this->op()->getId());
		}
	}

	/**
	 * 2017-01-15
	 * @used-by _handle()
	 */
	private function action() {
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
		/** @var string $action */
		$action = dftr($this->e()->ttCurrent(), [Ev::T_AUTHORIZE => AC::A, Ev::T_CAPTURE => AC::C]);
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
		$this->o()->save();
	}
}