<?php
use Magento\Sales\Model\Order\Creditmemo as CM;
/**
 * 2016-05-21
 * @see df_order_backend_url()
 * @param CM|int $cm
 * @return string
 */
function df_credit_memo_backend_url($cm) {
	return df_url_backend_ns('sales/order_creditmemo/view', ['creditmemo_id' => df_id($cm)]);
}


