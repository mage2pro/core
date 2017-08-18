<?php
use Magento\Sales\Model\Order\Creditmemo as CM;
/**
 * 2016-05-21
 * @see df_order_backend_url()
 * @param CM|int $cm
 * @return string
 */
function df_cm_backend_url($cm) {return df_url_backend_ns('sales/order_creditmemo/view', [
	'creditmemo_id' => df_idn($cm)
]);}

/**
 * 2016-08-29
 * Заметил, что в новых версиях Magento в момент выполнения @see \Df\Payment\Method::refund()
 * у @see \Magento\Sales\Model\Order\Creditmemo ещё отсутствует increment_id:
 * он устанавливается только при сохранении объекта:
 * @see \Magento\Sales\Model\ResourceModel\EntityAbstract::_beforeSave()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/ResourceModel/EntityAbstract.php#L123-L129
 * @param CM $cm
 */
function df_cm_set_increment_id(CM $cm) {
	if (!$cm->getIncrementId()) {
		$cm->setIncrementId(
			df_sales_seq_m()->getSequence(
				$cm->getEntityType(),
				$cm->getStore()->getGroup()->getDefaultStoreId()
			)->getNextValue()
		);
	}
}