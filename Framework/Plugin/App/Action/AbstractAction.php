<?php
namespace Df\Framework\Plugin\App\Action;
use Magento\Framework\App\Action\AbstractAction as Sb;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface as IRequest;
// 2017-08-06
final class AbstractAction {
	/**
	 * 2017-08-06
	 * The puprose of this plugin is to identify all the pages with our payment information block.
	 * https://github.com/mage2pro/allpay/blob/1.6.12/view/base/layout/df_payment_info.xml
	 * https://github.com/mage2pro/core/blob/2.10.4/Payment/view/frontend/layout/df_payment_info.xml
	 * https://github.com/mage2pro/moip/blob/1.0.6/view/frontend/layout/df_payment_info.xml
	 * @see \Magento\Framework\App\ActionInterface::execute()
	 * @param Sb $sb
	 */
	function beforeExecute(Sb $sb) {
		/** @var IRequest|Http $r */
		if (($r = $sb->getRequest()) instanceof Http && in_array($r->getFullActionName(), [
			'sales_order_view', 'sales_order_invoice_view', 'sales_guest_invoice', 'sales_guest_print'
			,'sales_guest_printinvoice', 'sales_guest_view', 'sales_order_invoice', 'sales_order_print'
			,'sales_order_printinvoice'
		])) {
			df_layout_update()->addHandle('df_payment_info');
		}
	}
}