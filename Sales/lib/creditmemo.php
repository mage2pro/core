<?php
/**
 * 2016-05-21
 * How to get an order backend URL programmatically? https://mage2.pro/t/1639
 * 2016-05-22
 * Даже если включена опция «Add Secret Key to URLs», адреса без ключей всё равно работают.
 * https://mage2.pro/tags/backend-url-secret-key
 * How to skip adding the secret key to a backend URL using the «_nosecret» parameter?
 * https://mage2.pro/t/1644
 * @param int $id
 * @return string
 */
function df_credit_memo_backend_url($id) {
	df_assert($id);
	return df_url_backend('sales/order_creditmemo/view', [
		'creditmemo_id' => $id, '_nosecret' => true
	]);
}


