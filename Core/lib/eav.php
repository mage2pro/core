<?php
use Magento\Eav\Model\Entity\Type as EntityType;
/**
 * 2015-10-12
 * @return \Magento\Eav\Model\Config
 */
function df_eav_config() {return df_o(\Magento\Eav\Model\Config::class);}

/**
 * 2015-10-12
 * @return EntityType
 */
function df_eav_customer() {return df_eav_config()->getEntityType('customer');}

/**
 * 2015-10-06
 * @used-by \Df\Customer\Setup\UpgradeData::_process()
 * @return \Magento\Eav\Setup\EavSetup
 */
function df_eav_setup() {return df_o(\Magento\Eav\Setup\EavSetup::class);}





