<?php
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-07-13
 * @used-by \Df\PaypalClone\TM::requestP()
 * @used-by \Df\PaypalClone\TM::responses()
 * @used-by \Df\StripeClone\Block\Info::prepare()
 * @used-by \Dfe\SecurePay\Signer\Response::values()
 * @param T $t
 * @param string|null $k [optional]
 * @param mixed|null $d [optional]
 * @return array(string => mixed)|mixed
 */
function df_trd(T $t, $k = null, $d = null) {return dfak(
	$t->getAdditionalInformation(T::RAW_DETAILS), $k, $d
);}

/**
 * 2016-07-10
 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::getTransactionAdditionalInfo()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * @used-by \Df\Payment\Method::iiaSetTR()
 * @used-by \Df\Payment\Method::iiaSetTRR()
 * @used-by \Df\Payment\W\Nav::op()
 * @param II|OP|QP|null $p
 * @param array(string => mixed) $v
 * @return void
 */
function df_trd_set(II $p, array $v) {$p->setTransactionAdditionalInfo(T::RAW_DETAILS, df_ksort($v));}