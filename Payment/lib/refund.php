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
 * @used-by \Dfe\TwoCheckout\Handler\RefundIssued::process()
 * @used-by \Dfe\Stripe\Handler\Charge\Refunded::process()
 *
 * @param P $p
 * @param I $i
 * @param string|int|float|null $amount [optional]
 * @return int|null
 */
function dfp_refund(P $p, I $i, $amount = null) {
	/** @var M $m */
	$m = $p->getMethodInstance();
	/** @var O $o */
	$o = $m->o();
	/** @var CML $cml */
	$cml = df_o(CML::class);
	$cml->setOrderId($o->getId());
	$cml->setInvoiceId($i->getId());
	if ($amount) {
		/**
		 * 2016-09-08
		 * Обработка частичного возврата.
		 * Делаем по аналогии с @see \Dfe\TwoCheckout\Handler\RefundIssued::cm()
		 *
		 * Произвожу расчёты в базовой валюте, чтобы не мешали курсовые колебания,
		 * которые могли произойти в период между платежом и возвратом.
		 */
		/** @var float $refundAmountB */
		$refundAmountB = $m->cToBase($m->amountParse($amount));
		/** @var float $invoiceAmountB */
		$invoiceAmountB = $i->getBaseGrandTotal();
		/** @var float $diffB */
		$diffB = $invoiceAmountB - $refundAmountB;
		if (!df_is0($diffB)) {
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
			 */
			$cml->setCreditmemo(['adjustment_negative' => df_currency_convert(
				$diffB, df_currency_base($o), $o->getOrderCurrency()
			)]);
		}
	}
	/** @var CM|false $cm */
	$cm = $cml->load();
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
		/** @var ICMS|CMS $cms */
		$cms = df_om()->create(ICMS::class);
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
	return $result;
}