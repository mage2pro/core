<?php
use Magento\Eav\Model\Entity\Type as EntityType;

/**
 * 2015-10-12
 * @used-by df_customer_att()
 */
function df_eav_customer():EntityType {return df_eav_config()->getEntityType('customer');}