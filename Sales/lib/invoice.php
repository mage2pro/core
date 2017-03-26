<?php
use Df\Core\Exception as DFE;
use Df\Sales\Model\Order\Payment as DfOP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Invoice as I;
/**
 * 2016-03-27
 * @used-by dfp_refund()
 * @param O $o
 * @param int $tid
 * @return I|null
 * @throws DFE
 */
function df_invoice_by_trans(O $o, $tid) {return DfOP::getInvoiceForTransactionId($o, $tid) ?: df_error(
	"No invoice found for the transaction {$tid}."
);}