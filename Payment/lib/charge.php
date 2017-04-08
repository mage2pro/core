<?php
use Df\Payment\Method as M;
use Magento\Sales\Model\Order as O;
/**
 * 2017-04-08
 * По аналогии с @see \Magento\Sales\Model\Order\Payment::processAction()
 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Sales/Model/Order/Payment.php#L420-L424
 * @used-by \Df\StripeClone\Method::charge()
 * @used-by \Dfe\CheckoutCom\Method::capturePreauthorized()
 * @param M $m
 * @param O|null $o [optional]
 * @return float в валюте заказа (платежа)
 */
function dfp_charge_amount(M $m, O $o = null) {return dfcf(function(M $m, O $o) {return
	$m->cFromBase($o->getBaseTotalDue())
;}, [$m, $o ?: $m->o()]);}