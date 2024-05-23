<?php
use Magento\Eav\Model\Entity\Type as T;
/**
 * 2019-03-06
 * @used-by \Df\Customer\AddAttribute\Address::p()
 */
function df_eav_ca():T {return df_eav_type('customer_address');}

/**
 * 2015-10-12
 * @used-by df_customer_att()
 */
function df_eav_customer():T {return df_eav_type('customer');}

/**
 * 2024-05-23 "Implement `df_eav_type()`": https://github.com/mage2pro/core/issues/388
 * @used-by df_eav_ca()
 * @used-by df_eav_customer()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 */
function df_eav_type(string $t):T {return df_eav_config()->getEntityType($t);}