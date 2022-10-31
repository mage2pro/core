<?php
use Magento\Eav\Model\Entity\Type as EntityType;

/**
 * 2019-03-06
 * @used-by \Df\Customer\AddAttribute\Address::p()
 */
function df_eav_ca():EntityType {return df_eav_config()->getEntityType('customer_address');}