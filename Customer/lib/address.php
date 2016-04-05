<?php
use Magento\Customer\Model\Address as CA;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\Customer;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
/**
 * 2016-04-04
 * @param AA|CA|QA $a
 * @return Customer|Quote|null
 */
function df_address_owner(AA $a) {
	return $a instanceof CA ? $a->getCustomer() : ($a instanceof QA ? $a->getQuote() : null);
}

/**
 * 2016-04-05
 * @return AddressRegistry
 */
function df_address_registry() {return df_o(AddressRegistry::class);}

/**
 * 2016-04-04
 * @param AA|CA|QA $a
 * @return StoreInterface|Store
 */
function df_address_store(AA $a) {
	/** @var Customer|Quote|null $owner */
	$owner = df_address_owner($a);
	return $owner ? df_store($owner->getStore()) : null;
}

