<?php
use Df\Payment\Method as M;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Invoice as I;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as IC;
/**
 * 2017-04-08
 * By analogy with @see \Magento\Sales\Model\Order\Payment::processAction()
 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Sales/Model/Order/Payment.php#L420-L424
 * The result is in the order/payment currency.
 * @used-by \Df\Payment\Operation\Source\Order::amount()
 * @used-by \Df\Payment\Operation\Source\Quote::amount()
 * @used-by \Df\StripeClone\Method::charge()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\CheckoutCom\Method::capturePreauthorized()
 * @param O|Q|I|CM|null $d [optional]
 */
function dfp_due(M $m, $d = null):float {
	$d = $d ?: ($m->ii()->getCreditmemo() ?: $m->oq());
	# 2018-10-06 This code handles the backend partial capture of a preauthorized bank card payment.
	if (df_is_o($d)) {
		$ic = $d->getInvoiceCollection(); /** @var IC $ic */
		if ($ic->count()) {
			$i = $ic->getLastItem(); /** @var I $i */
			if (!$i->getId()) {
				$d = $i;
			}
		}
	}
	return dfcf(function(M $m, $d) {/**@var O|Q|I|CM $d */ return $m->cFromBase(
		df_is_o($d) ? $d->getBaseTotalDue() : (
			$d instanceof CM || $d instanceof I || df_is_q($d) ? $d->getBaseGrandTotal() : df_error(
				'Invalid document class: %s.', df_cts($d)
			)
		)
	);}, [$m, $d]);
}