<?php
use Magento\Catalog\Model\Category;
use Magento\Customer\Api\AddressMetadataInterface as ICustomerAddressMetadata;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Type as T;
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
 * 2024-05-23
 * 1) "Implement `df_eav_category()`": https://github.com/mage2pro/core/issues/389
 * 2) The 'catalog_category' constant is also duplicated here:
 * @see \Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE
 * @see \Magento\MediaContentCatalog\Model\ResourceModel\GetAssetIdsByCategoryStore::ENTITY_TYPE
 */
function df_eav_category():T {return df_eav_type(Category::ENTITY);}

/**
 * 2015-10-12
 * 2024-05-23
 * The 'customer' constant is also duplicated here:
 * @see \Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER
 * @see \Magento\Customer\Api\CustomerMetadataManagementInterface::ENTITY_TYPE_CUSTOMER
 * @used-by df_customer_att()
 */
function df_eav_customer():T {return df_eav_type(Customer::ENTITY);}

/**
 * 2024-05-23 "Implement `df_eav_type()`": https://github.com/mage2pro/core/issues/388
 * @used-by df_eav_ca()
 * @used-by df_eav_category()
 * @used-by df_eav_customer()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 */
function df_eav_type(string $t):T {return df_eav_config()->getEntityType($t);}