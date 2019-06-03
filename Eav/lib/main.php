<?php
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Model\AbstractModel as Model;

/**
 * 2015-10-12
 * @used-by df_eav_customer()
 * @used-by df_eav_ca()
 * @used-by \Df\Framework\Upgrade\Data::attributeCA()
 * @return \Magento\Eav\Model\Config
 */
function df_eav_config() {return df_o(\Magento\Eav\Model\Config::class);}

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
 * 2015-10-06
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \Df\Framework\Upgrade\Data::attributeCA()
 * @return EavSetup
 */
function df_eav_setup() {return df_o(EavSetup::class);}

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