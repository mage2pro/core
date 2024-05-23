<?php
use Magento\Sales\Model\Order\Creditmemo as C;
/**
 * 2016-05-21
 * @see df_order_backend_url()
 * @used-by \Dfe\TwoCheckout\Method::_refund()
 * @param C|int $c
 */
function df_cm_backend_url($c):string {return df_url_backend_ns('sales/order_creditmemo/view', [
	'creditmemo_id' => df_idn($c)
]);}

/**
 * 2016-08-29
 * Заметил, что в новых версиях Magento в момент выполнения @see \Df\Payment\Method::refund()
 * у @see \Magento\Sales\Model\Order\Creditmemo ещё отсутствует increment_id:
 * он устанавливается только при сохранении объекта: @see \Magento\Sales\Model\ResourceModel\EntityAbstract::_beforeSave()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/ResourceModel/EntityAbstract.php#L123-L129
 * @used-by \Df\Payment\Method::refund()
 */
function df_cm_set_increment_id(C $c):void {
	if (!$c->getIncrementId()) {
		$c->setIncrementId(df_sales_seq_m()->getSequence(
			$c->getEntityType()
			,$c->getStore()->getGroup()->getDefaultStoreId()
		)->getNextValue());
	}
}