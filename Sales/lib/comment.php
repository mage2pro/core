<?php
use Magento\Sales\Api\Data\OrderStatusHistoryInterface as IHistory;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Status\History;
/**
 * 2017-04-13
 * @used-by df_mail_order()
 * @used-by \Df\Payment\CustomerReturn::execute()   
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @param O $o
 * @param $text
 * @param $visibleOnFront
 * @param $customerNotified
 */
function df_order_comment(O $o, $text, $visibleOnFront = false, $customerNotified = false) {
	/** @var History|IHistory $h */
	$h = $o->addStatusHistoryComment(__($text));
	$h->setIsVisibleOnFront($visibleOnFront);
	$h->setIsCustomerNotified($customerNotified);
	$h->save();
}