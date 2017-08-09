<?php
namespace Df\Sales\Model\Order;
use Df\Sales\Model\Order\Invoice as DfInvoice;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Creditmemo;
/**
 * 2016-03-27
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @method Creditmemo getCreatedCreditmemo()
 * @method Invoice|DfInvoice|null getCreatedInvoice()
 * @method string|null getRefundTransactionId()
 * https://github.com/magento/magento2/blob/ffea3cd/app/code/Magento/Sales/Model/Order/Payment.php#L652
 */
class Payment extends OP {
	/**
	 * 2016-05-08
	 * 2017-03-26
	 * Вызов этого метода приводит к добавлению транзакции:
	 * https://github.com/mage2pro/core/blob/2.4.2/Payment/W/Nav.php#L100-L114
	 * @used-by dfp_action()
	 * @param OP $op
	 * @param string $action
	 */
	final static function action(OP $op, $action) {
		$op->processAction($action, $o = df_order($op));
		$op->updateOrder($o, O::STATE_PROCESSING, df_order_ds(O::STATE_PROCESSING), true);
	}	

	/**
	 * 2016-03-27
	 * https://mage2.pro/t/1031
	 * The methods
	 * @see \Magento\Sales\Model\Order\Payment\Operations\AbstractOperation::getInvoiceForTransactionId()
	 * and @see \Magento\Sales\Model\Order\Payment::_getInvoiceForTransactionId()
	 * duplicate almost the same code
	 * @used-by df_invoice_by_trans()
	 * @param O $o
	 * @param int $tid
	 * @return Invoice|null
	 */
	final static function getInvoiceForTransactionId(O $o, $tid) {
		$i = df_new_om(__CLASS__); /** @var Payment $i */
		$i->setOrder($o);
		return $i->_getInvoiceForTransactionId($tid);
	}
	
	/**
	 * 2017-02-09
	 * Код страны, выпустившей банковскую карту.
	 * @used-by \Dfe\Paymill\Facade\Charge::card()
	 * https://github.com/mage2pro/paymill/blob/0.2.0/Method.php?ts=4#L37-L39
	 */
	const COUNTRY = 'country';	
}