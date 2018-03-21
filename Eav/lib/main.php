<?php
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Framework\Model\AbstractModel as Model;
/**
 * 2016-08-22
 * @param Model $model
 */
function df_eav_partial_save(Model $model) {
	/** @var AbstractEntity $resource */
	$resource = $model->getResource();
	$resource->isPartialSave(true);
	try {$model->save();}
	finally {$resource->isPartialSave(false);}
}

/**
 * 2016-12-03
 * https://mage2.pro/t/2358
 * I implemented it by analogy with a similar solution for Magento 1.x:
 * http://magento.stackexchange.com/a/86146
 * @param Model $model
 * @param string $attName
 * @param mixed $attValue
 */
function df_eav_update(Model $model, $attName, $attValue) {
	df_param_sne($attName, 1);
	$model[$attName] = $attValue;
	/** @var AbstractEntity $resource */
	$resource = df_ar($model->getResource(), AbstractEntity::class);
	$resource->saveAttribute($model, $attName);
}