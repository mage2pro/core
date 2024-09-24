<?php
use Magento\Sales\Api\Data\OrderStatusHistoryInterface as IHistory;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Status\History;
/**
 * 2017-04-13
 * @used-by df_mail_order()
 * @used-by Df\Payment\CustomerReturn::execute()   
 * @used-by Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @used-by Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 */
function df_order_comment(O $o, string $text, bool $visibleOnFront = false, bool $customerNotified = false):void {
	$h = $o->addStatusHistoryComment(__($text)); /** @var History|IHistory $h */
	$h->setIsVisibleOnFront($visibleOnFront);
	$h->setIsCustomerNotified($customerNotified);
	$h->save();
}