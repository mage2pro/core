<?php
/**
 * 2017-08-04
 * It detects a sales transactional email sending process (order, invoice, credit memo, etc.).
 * https://mage2.pro/t/4236
 * Previously, I used to detect it by a plugin to
 * @see \Magento\Sales\Model\Order\Email\Sender\OrderSender::send():
 * https://github.com/mage2pro/core/blob/2.10.1/Sales/Plugin/Model/Order/Email/Sender/OrderSender.php#L1-L38
 * The plugin detected only the order transactional email sending process.
 * The new solution detects all the sales transactional email sending processes:
 * for orders, invoices, credit memos, etc.
 * @used-by \Df\Payment\Block\Info::_toHtml()
 * @used-by \Dfe\Moip\CardFormatter::label()
 */
function df_sales_email_sending():bool {return !!df_find(function(array $i) {return df_contains(
	dfa($i, 'class'), 'EmailSender', 'Email\Sender'
);}, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));}

/**
 * 2016-01-29
 * @return int|false
 */
function df_sales_entity_type_index(string $t) {return array_search($t, array_values(df_sales_entity_types()));}

/**
 * 2016-01-11
 * @used-by df_sales_entity_type_index()
 * @used-by \Dfe\SalesSequence\Config\Affix\Element::rows()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::afterCommitCallback()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::nextNumbersFromDb()
 * @used-by \Dfe\SalesSequence\Config\Next\Element::columns()
 * @return array(string => string)
 */
function df_sales_entity_types():array {return [
	'Order' => 'order', 'Invoice' => 'invoice', 'Shipment' => 'shipment', 'Credit Memo' => 'creditmemo'
];}