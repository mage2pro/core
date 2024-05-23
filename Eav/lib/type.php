<?php
use Magento\Eav\Model\Entity\Type as T;
use Magento\Customer\Api\AddressMetadataInterface as ICustomerAddressMetadata;
use Magento\Customer\Api\CustomerMetadataInterface as ICustomerMetadata;
/**
 * 2019-03-06
 * @used-by \Df\Customer\AddAttribute\Address::p()
 * 2024-05-23
 * The 'customer_address' constant is also duplicated here:
 * @see \Magento\Customer\Api\AddressMetadataManagementInterface::ENTITY_TYPE_ADDRESS
 * @see \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY
 */
function df_eav_ca():T {return df_eav_type(ICustomerAddressMetadata::ENTITY_TYPE_ADDRESS);}

/**
 * 2015-10-12
 * @used-by df_customer_att()
 */
function df_eav_customer():T {return df_eav_type(ICustomerMetadata::ENTITY_TYPE_CUSTOMER);}

/**
 * 2024-05-23 "Implement `df_eav_type()`": https://github.com/mage2pro/core/issues/388
 * @used-by df_eav_ca()
 * @used-by df_eav_customer()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 */
function df_eav_type(string $t):T {return df_eav_config()->getEntityType($t);}