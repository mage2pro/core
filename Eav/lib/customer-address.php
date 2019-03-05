<?php
use Magento\Eav\Model\Entity\Type as EntityType;

/**
 * 2019-03-06
 * @used-by \Df\Framework\Upgrade\Data::attributeCA()
 * @return EntityType
 */
function df_eav_ca() {return df_eav_config()->getEntityType('customer_address');}