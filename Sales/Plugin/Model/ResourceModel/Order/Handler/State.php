<?php
// 2017-01-18
namespace Df\Sales\Plugin\Model\ResourceModel\Order\Handler;
use Df\Sales\Model\Order as DFO;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\ResourceModel\Order\Handler\State as Sb;
class State {
	/**
	 * 2017-01-18
	 * Цель плагина — сохранение для заказа, для которого был осуществлён частичный возврат,
	 * состояния «Processing» вместо состояние «Complete», которое норовит установить ядро.
	 * @see \Magento\Sales\Model\ResourceModel\Order\Handler\State::check()
	 *
	 * Причём ядро норовит это сделать в 2 местах:
	 *
	 * 1) @see \Magento\Sales\Model\Order\Payment::refund()
		$orderState = $this->getOrderStateResolver()->getStateForOrder($this->getOrder());
		$this->getOrder()
			->addStatusHistoryComment(
				$message,
				$this->getOrder()->getConfig()->getStateDefaultStatus($orderState)
			)->setIsCustomerNotified($creditmemo->getOrder()->getCustomerNoteNotify());
	https://github.com/magento/magento2/blob/91aa307/app/code/Magento/Sales/Model/Order/Payment.php#L707-L712

 	* @see \Magento\Sales\Model\Order\StateResolver::getStateForOrder()
		if (!$order->isCanceled() && !$order->canUnhold()
			&& !$order->canInvoice() && !$order->canShip()
		) {
			if ($this->isOrderComplete($order)) {
				$orderState = Order::STATE_COMPLETE;
			} elseif ($this->isOrderClosed($order, $arguments)) {
				$orderState = Order::STATE_CLOSED;
			}
		}
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/StateResolver.php#L84-L90
	 *
	 * Если бы ядро норовило установить состояние «Complete» только здесь,
	 * то плагин нам не был бы необходим: мы бы решили нашу проблему обработкой события
	 * sales_order_payment_refund:
 * @see \Magento\Sales\Model\Order\Payment::refund()
        $this->_eventManager->dispatch(
            'sales_order_payment_refund',
            ['payment' => $this, 'creditmemo' => $creditmemo]
        );
 * https://github.com/magento/magento2/blob/91aa307/app/code/Magento/Sales/Model/Order/Payment.php#L713-L716
	 *
	 * 2) @see \Magento\Sales\Model\ResourceModel\Order\Handler\State::check()
			if (!$order->isCanceled() && !$order->canUnhold()
				&& !$order->canInvoice() && !$order->canShip()
			) {
				if (0 == $order->getBaseGrandTotal() || $order->canCreditmemo()) {
					if ($order->getState() !== Order::STATE_COMPLETE) {
						$order->setState(Order::STATE_COMPLETE)
							->setStatus($order->getConfig()->getStateDefaultStatus(
								Order::STATE_COMPLETE)
							);
					}
				} elseif (floatval($order->getTotalRefunded())
					|| !$order->getTotalRefunded() && $order->hasForcedCanCreditmemo()
				) {
					if ($order->getState() !== Order::STATE_CLOSED) {
						$order->setState(Order::STATE_CLOSED)
							->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED));
					}
				}
			}
	 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/ResourceModel/Order/Handler/State.php#L27-L31
	 *
	 * Как видно из этого кода, ядро будет в этой точке программы
	 * повторно пытаться насильно установить нашему заказу состояние «Complete».
	 *
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @param O|DFO $o
	 * @return string
	 */
	public function aroundCheck(Sb $sb, \Closure $proceed, O $o) {
		$proceed($o);
		/** @var OP|null $op */
		$op = $o->getPayment();
		if ($op && dfp_is_my($op)) {
			/** @var CM|null $cm */
			$cm = $op->getCreditmemo();
			if ($cm && !df_is0(floatval($op->getBaseAmountPaid()) - $cm->getBaseGrandTotal())) {
				/**
				 * 2017-01-19
				 * @todo На самом деле, это не совсем правильно!
				 * Надо переводить заказ не в состояние «Processing»,
				 * а в то состояние, которое было у заказа до его насильственного переведения
				 * в состояние «Complete» методом @see \Magento\Sales\Model\Order\Payment::refund().
				 */
				$o->setState(O::STATE_PROCESSING);
				$o->setStatus($o->getConfig()->getStateDefaultStatus(O::STATE_PROCESSING));
				/**
				 * 2017-01-19
				 * Когда мы выполняем возврат на стороне Magento,
				 * то в результате соответствующего запроса к API платёжной системы,
				 * платёжная система нам пришлёт оповещение о нашем же возврате,
				 * и избежать этого мы не можем.
				 * Так вот, нам нужно, чтобы обработчик этого оповещения
				 * @see \Df\StripeClone\WebhookStrategy\Charge\Refunded::handle()
				 * не выполнил повторный возврат той же самой суммы.
				 *
				 * Раньше это не было проблемой,
				 * потому что при возврате ядро переводило заказ в состояние «Complete».
				 * Однако вчера мы для частичного возврата стали оставлять у заказа состояние «Processing».
				 * И вот теперь появилась необходимость избегать повторного возврата.
				 *
				 * Попытка №1
				 * Первой мыслью была установка заказу флага ForcedCanCreditmemo:
				 * 	$o->setForcedCanCreditmemo(false);
				 * Этот флаг проверяется в методе
				 * @used-by \Magento\Sales\Model\Order::canCreditmemo(),
				 * который, в свою очередь, вызывается из
				 * @see \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader::_canCreditmemo()
				 * И когда этот флаг установлен в false, то @see dfp_refund() ничего делать не будет.
				 * Однако флаг ForcedCanCreditmemo не сохраняется в базе данных,
				 * поэтому такое решение не работает!
				 *
				 * Попытка №2
				 * Второй простой способ — проверять, есть ли уже у заказа документы типа «возврат»
				 * (credit memo), и не допускать возврат, когда такой документ уже имеется.
				 * Однако Magento, вообще говоря, способна учитывать множество возвратов
				 * для одного и того же заказа.
				 * Даже вкладка «Credit Memos» на административном экране заказа
				 * отображает таблицу, которая может отображать множество возвратов.
				 * Онако в реальности Magento закрывает после первого же возврата:
				 * переводит в состояние «Closed» при полном возврате
				 * и «Complete» при частичном возврате.
				 *
				 * Я посчитал, что это неправильно, и теперь при частичном возврате
				 * оставляю заказу состояние «Processing».
				 * @todo А ещё лучше было бы переводить заказ не в состояние «Processing»,
				 * а в то состояние, которое было у заказа до его насильственного переведения
				 * в состояние «Complete» методом @see \Magento\Sales\Model\Order\Payment::refund().
				 *
				 * Вот теперь, когда после частичного возврата
				 * заказ больше не попадает в состояние «Complete»,
				 * открыта дорога нескольким возвратам для одного и того же заказа,
				 * причём проверенные мной Stripe-подобные системы (Stripe, Omise) это допускают.
				 *
				 * Поэтому я и отказался от решения №2, хотя оно и должно быть работоспособным.
				 *
				 * Попытка №3
				 * API Stripe-подобной платёжной системы в ответ на запрос «refund»
				 * возвращает идентификатор транзакции.
				 * Вот эти идентификаторы мы можем запоминать
				 * и затем в обработчике оповещений от платёжной системы смотреть,
				 * была ли транзакция с данным идентфикатором инициирована нами же,
				 * и если да — то ничего не делать.
				 * Это решение выглядит идеальным, и, более того, оно универсально:
				 * защищает нас от повторной обработки не только возвратов,
				 * но и других типов операций.
				 */
				$o->setForcedCanCreditmemo(false);
			}
		}
		return $sb;
	}
}


