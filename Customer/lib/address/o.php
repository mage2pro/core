<?php
use Magento\Customer\Api\AddressRepositoryInterface as IAddressRep;
use Magento\Customer\Helper\Address as AddressH;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\ResourceModel\AddressRepository as AddressRep;

/**
 * 2019-06-01
 * @used-by \KingPalm\B2B\Block\Registration::region()
 */
function df_address_h():AddressH {return df_o(AddressH::class);}

/**
 * 2016-04-05
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::aroundSave()
 */
function df_address_registry():AddressRegistry {return df_o(AddressRegistry::class);}

/**
 * 2021-05-07
 * @used-by \Df\Quote\Plugin\Model\QuoteAddressValidator::doValidate()
 * @return IAddressRep|AddressRep
 */
function df_customer_address_rep() {return df_o(IAddressRep::class);}