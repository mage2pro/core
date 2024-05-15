<?php
use Magento\Eav\Model\Entity\Type as T;

/**
 * 2015-10-12
 * @used-by df_customer_att()
 */
function df_eav_customer():T {return df_eav_config()->getEntityType('customer');}