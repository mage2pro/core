<?php
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Creditmemo as C;
use Magento\Sales\Model\Order\Creditmemo\Item as CI;
use Magento\Sales\Model\Order\Invoice as I;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Item as II;

/**
 * 2020-03-13
 * @used-by df_is_sales_doc()
 * @used-by df_sales_doc()
 * @param mixed $v
 */
function df_is_credit_memo($v):bool {return $v instanceof C;}

/**
 * 2020-03-13
 * @used-by df_is_sales_doc()
 * @used-by df_sales_doc()
 * @param mixed $v
 */
function df_is_invoice($v):bool {return $v instanceof I;}

/**
 * 2020-03-13
 * @see df_is_oq()
 * @used-by df_sales_doc()
 * @param mixed $v
 */
function df_is_sales_doc($v):bool {return df_is_oq($v) || df_is_credit_memo($v) || df_is_invoice($v);}

/**
 * 2020-03-13
 * @see df_oq()
 * @used-by Customweb\RealexCw\Helper\InvoiceItem::getInvoiceItems() tradefurniturecompany.co.uk
 * @param C|CI|I|II|O|OI|Q|QI $v
 * @return C|I|O|Q
 */
function df_sales_doc($v) {return df_is_sales_doc($v) ? $v : (df_is_oqi($v) ? df_oq($v) : (
	df_is_credit_memo($v) ? $v->getCreditmemo() : (df_is_invoice($v) ? $v->getInvoice() : df_error())
));}