<?php
use Df\Payment\Method as M;
use Magento\Sales\Api\CreditmemoManagementInterface as ICMS;
use Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader as CML;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Invoice as I;
use Magento\Sales\Model\Order\Payment as P;
use Magento\Sales\Model\Service\CreditmemoService as CMS;
/**   
 * 2016-09-08
 * 2016-03-27
 * «How is an online refunding implemented?» https://mage2.pro/t/959
 * Сначала хотел cделать по аналогии с @see \Magento\Paypal\Model\Ipn::_registerPaymentRefund()
 * https://github.com/magento/magento2/blob/9546277/app/code/Magento/Paypal/Model/Ipn.php#L467-L501
 * Однако используемый там метод @see \Magento\Sales\Model\Order\Payment::registerRefundNotification()
 * нерабочий: «Invalid method Magento\Sales\Model\Order\Creditmemo::register»
 * https://mage2.pro/t/1029
 * Поэтому делаю по аналогии с @see \Magento\Sales\Controller\Adminhtml\Order\Creditmemo\Save::execute()
 *
 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\Charge\Refunded::process()
 * @used-by \Dfe\TwoCheckout\Handler\RefundIssued::process()
 *
 * @param P $p
 * @param int $tid
 * @param string|int|float|null $a [optional]	 в валюте заказа (платежа), в формате платёжной системы (копейках).
 * @return int|null
 */
function dfp_refund(P $p, $tid, $a = null) {
	/** @var I $i */
	/** @var O $o */
	$i = df_invoice_by_trans($o = df_order($p), $tid);
	/** @var M $m */
	$m = dfpm($p);
	/** @var CML $cml */
	$cml = df_o(CML::class);
	$cml->setOrderId($o->getId())->setInvoiceId($i->getId());
	if ($a) {
		/**
		 * 2016-09-08
		 * Обработка частичного возврата.
		 * Делаем по аналогии с @see \Dfe\TwoCheckout\Handler\RefundIssued::cm()
		 *
		 * Произвожу расчёты в базовой валюте, чтобы не мешали курсовые колебания,
		 * которые могли произойти в период между платежом и возвратом.
		 *
		 * 2017-01-18
		 * Более того, ядро требует данных в базовой валюте! (смотрите ниже)
		 * @see \Magento\Sales\Model\Order\Creditmemo::setAdjustmentNegative()
		 */
		$refundAmountB = $m->cToBase($m->amountParse($a)); /** @var float $refundAmountB */
		/** @var float $diffB */
		$diffB = $i->getBaseGrandTotal() - $refundAmountB;
		if (!dff_eq0($diffB)) {
			/**
			 * 2016-05-23
			 * https://mage2.pro/tags/credit-memo-adjustment
			 *
			 * Стек вызова:
			 * 1) @used-by \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader::load()
			 * https://github.com/magento/magento2/blob/b366da/app/code/Magento/Sales/Controller/Adminhtml/Order/CreditmemoLoader.php#L186
			 * 2) @used-by \Magento\Sales\Model\Order\CreditmemoFactory::createByInvoice()
			 * https://github.com/magento/magento2/blob/b366da/app/code/Magento/Sales/Model/Order/CreditmemoFactory.php#L155
			 * 3) @used-by \Magento\Sales\Model\Order\CreditmemoFactory::initData()
			 * https://github.com/magento/magento2/blob/b366da/app/code/Magento/Sales/Model/Order/CreditmemoFactory.php#L244-L246
			 *
			 * 2017-01-18
			 * Значение должно быть в базовой валюте!
			 * @used-by \Magento\Sales\Model\Order\Creditmemo::setAdjustmentNegative()
			 * Стек вызовов смотрите выше.
			 */
			$cml->setCreditmemo([
				// 2017-04-10
				// Алгоритм выглядит, как сумасшествие, но именно он даёт верный результат.
				// Реально сумасшедшим тут является ядро, ради которого приходится так изголяться.
				'adjustment_negative' => $o->getBaseTotalRefunded() ? -abs($refundAmountB) : abs($diffB)
			]);
		}
	}
	/** @var CM|false $cm */
	/**
	 * 2017-01-19
	 * Когда мы выполняем возврат на стороне Magento,
	 * то в результате соответствующего запроса к API платёжной системы,
	 * платёжная система нам пришлёт оповещение о нашем же возврате, и избежать этого мы не можем.
	 * Так вот, нам нужно, чтобы обработчик этого оповещения
	 * @see \Df\Payment\W\Strategy\Refund::_handle()
	 * не выполнил повторный возврат той же самой суммы.
	 *
	 * Раньше это не было проблемой, потому что при возврате ядро переводило заказ в состояние «Complete».
	 * Однако вчера мы для частичного возврата стали оставлять у заказа состояние «Processing»:
	 * @see \Df\Sales\Plugin\Model\ResourceModel\Order\Handler\State::aroundCheck()
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
	 * Второй простой способ — проверять, есть ли уже у заказа документы типа «возврат» (credit memo),
	 * и не допускать возврат, когда такой документ уже имеется.
	 * Однако Magento, вообще говоря, способна учитывать множество возвратов для одного и того же заказа.
	 * Даже вкладка «Credit Memos» на административном экране заказа
	 * отображает таблицу, которая может отображать множество возвратов.
	 * Онако в реальности Magento закрывает после первого же возврата:
	 * переводит в состояние «Closed» при полном возврате и «Complete» при частичном возврате.
	 *
	 * Я посчитал, что это неправильно, и теперь при частичном возврате
	 * оставляю заказу состояние «Processing».
	 * @todo А ещё лучше было бы переводить заказ не в состояние «Processing»,
	 * а в то состояние, которое было у заказа до его насильственного переведения
	 * в состояние «Complete» методом @see \Magento\Sales\Model\Order\Payment::refund().
	 *
	 * Вот теперь, когда после частичного возврата заказ больше не попадает в состояние «Complete»,
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
	$cm = df_try(
		function() use($cml) {return $cml->load();}
		/**
		 * 2017-10-21
		 * The lennyshoe.com website is experiencing the following error here:
		 * «Could not save credit memo»:
		 * «Division by zero in magento/module-sales/Model/Order/Creditmemo/Item.php»
		 * https://github.com/mage2pro/stripe/issues/43
		 * I am unable to reproduce it myself.
		 */
		,function(\Exception $e) {return 'lennyshoe.com' === df_domain_current() ? null : df_error($e);}
	);
	/**
	 * 2016-12-30
	 * @uses \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader::load()
	 * возвращает false, если возврат уже был выполнен ранее:
	 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Controller/Adminhtml/Order/CreditmemoLoader.php#L190-L192
	 * Мы не должны считать это исключительной ситуацией.
	 * В документации к Stripe, например, явно сказано:
	 * «Webhook endpoints may occasionally receive the same event more than once.
	 * We advise you to guard against duplicated event receipts
	 * by making your event processing idempotent.»
	 * https://stripe.com/docs/webhooks#best-practices
	 */
	/** @var int|null $result */
	if (!$cm) {
		$result = null;
	}
	else {
		/**
		 * 2016-03-28
		 * Важно! Иначе order загрузит payment автоматически вместо нашего,
		 * и флаг @see \Dfe\Stripe\Method::WEBHOOK_CASE будет утерян
		 */
		$cm->getOrder()->setData(O::PAYMENT, $p);
		$cms = df_new_om(ICMS::class); /** @var ICMS|CMS $cms */
		/**
		 * 2017-10-21
		 * The lennyshoe.com website is experiencing the following error here sometimes:
		 * «Could not save credit memo»: «The most money available to refund is <...>»
		 * https://github.com/mage2pro/stripe/issues/42
		 * I am unable to reproduce it myself.
		 */
		$cms->refund($cm, false);
		/**
		 * 2016-03-28
		 * @todo Надо отослать покупателю письмо-оповещение о возврате оплаты.
		 * 2016-05-15
		 * Что интересно, при возврате из административной части Magento 2
		 * покупатель тоже не получает уведомление.
		 */
		$result = $cm->getId();
	}
	/**
	 * 2017-01-18
	 * Если возврат выполнен частично, то мы, в отличие от ядра,
	 * сохраняем для заказа состояние «Processing»:
	 * @see \Df\Sales\Plugin\Model\ResourceModel\Order\Handler\State::aroundCheck()
	 */
	return $result;
}