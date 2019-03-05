<?php
use Magento\Eav\Model\Entity\Type as EntityType;

/**
 * 2015-10-12
 * @return EntityType
 */
function df_eav_customer() {return df_eav_config()->getEntityType('customer');}