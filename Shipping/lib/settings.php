<?php
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**              
 * 2018-04-21
 * @used-by \Df\Shipping\Action::s()
 * @used-by \Doormall\Shipping\Plugin\Sales\Model\Order::afterGetShippingDescription()
 * @param M|II|OP|QP|O|Q|T|object|string|null $m
 * @param string|null $k [optional]
 * @return S|mixed
 */
function dfss($m, $k = null) {return dfsm($m)->s($k);}