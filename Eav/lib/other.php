<?php
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Framework\Model\AbstractModel as Model;

/**
 * 2016-08-22
 * @used-by df_ci_save()
 */
function df_eav_partial_save(Model $m):void {
	$r = $m->getResource(); /** @var AbstractEntity $r */
	$r->isPartialSave(true);
	try {$m->save();}
	finally {$r->isPartialSave(false);}
}

/**
 * 2016-12-03
 * https://mage2.pro/t/2358
 * I implemented it by analogy with a similar solution for Magento 1.x: http://magento.stackexchange.com/a/86146
 * @param mixed $attValue
 */
function df_eav_update(Model $m, string $attName, $attValue):void {
	df_param_sne($attName, 1);
	$m[$attName] = $attValue;
	$r = df_ar($m->getResource(), AbstractEntity::class); /** @var AbstractEntity $r */
	$r->saveAttribute($m, $attName);
}