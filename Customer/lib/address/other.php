<?php
use Magento\Customer\Api\AddressRepositoryInterface as IAddressRep;
use Magento\Customer\Helper\Address as AddressH;
use Magento\Customer\Model\Address as CA;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\AddressRepository as AddressRep;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;

/**
 * 2017-04-22
 * An UML relationship between the address classes:
 * @see \Magento\Customer\Model\Address
 * @see \Magento\Quote\Model\Quote\Address
 * @see \Magento\Sales\Model\Order\Address
 * https://mage2.pro/t/3634
 */

/**
 * 2019-06-01
 * @used-by \KingPalm\B2B\Block\Registration::region()
 */
function df_address_h():AddressH {return df_o(AddressH::class);}

/**
 * 2016-04-04
 * @used-by df_address_store()
 * @param AA|CA|QA|OA $a
 * @return Customer|Quote|Order|null
 */
function df_address_owner($a) {return df_is_ca($a) ? $a->getCustomer() : (
	df_is_qa($a) ? $a->getQuote() : (df_is_oa($a) ? $a->getOrder() : null)
);}

/**
 * 2016-04-05
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::aroundSave()
 */
function df_address_registry():AddressRegistry {return df_o(AddressRegistry::class);}

/**
 * 2016-04-04  
 * @used-by \Dfe\Customer\Plugin\Customer\Model\Address\AbstractAddress::afterValidate()
 * @param AA|CA|QA|OA $a
 * @return StoreInterface|Store
 */
function df_address_store($a) {/** @var Customer|Quote|null $owner */return
	($owner = df_address_owner($a)) ? df_store($owner->getStore()) : null
;}

/**
 * 2021-05-07
 * @used-by \Df\Quote\Plugin\Model\QuoteAddressValidator::doValidate()
 * @return IAddressRep|AddressRep
 */
function df_customer_address_rep() {return df_o(IAddressRep::class);}