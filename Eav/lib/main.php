<?php
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Framework\Model\AbstractModel;
/**
 * 2016-08-22
 * @param AbstractModel $model
 * @return void
 */
function df_eav_partial_save(AbstractModel $model) {
	/** @var AbstractEntity $resource */
	$resource = $model->getResource();
	$resource->isPartialSave(true);
	try {$model->save();}
	finally {$resource->isPartialSave(false);}
}


