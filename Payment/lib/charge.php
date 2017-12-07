<?php
use Df\Payment\Method as M;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Quote\Model\Quote as Q;
/**
 * 2017-04-08
 * By analogy with @see \Magento\Sales\Model\Order\Payment::processAction()
 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Sales/Model/Order/Payment.php#L420-L424
 * @used-by \Df\Payment\Operation\Source\Order::amount()
 * @used-by \Df\Payment\Operation\Source\Quote::amount()
 * @used-by \Df\StripeClone\Method::charge()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\CheckoutCom\Method::capturePreauthorized()
 * @param M $m
 * @param O|Q|CM|null $d [optional]
 * @return float в валюте заказа (платежа)
 */
function dfp_due(M $m, $d = null) {return dfcf(function(M $m, $d) {/**@var O|Q|CM $d */ return $m->cFromBase(
	df_is_o($d) ? $d->getBaseTotalDue() : (
		$d instanceof CM || df_is_q($d) ? $d->getBaseGrandTotal() : df_error(
			'Invalid document class: %s.', df_cts($d)
		)
	)
);}, [$m, $d ?: ($m->ii()->getCreditmemo() ?: $m->o())]);}