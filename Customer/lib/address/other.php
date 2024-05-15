<?php
use Magento\Customer\Model\Address as CA;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Magento\Customer\Model\Customer;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;

/**
 * 2017-04-22
 * Relationshis between address classes: https://mage2.pro/t/3634
 * @see \Magento\Customer\Model\Address
 * @see \Magento\Quote\Model\Quote\Address
 * @see \Magento\Sales\Model\Order\Address
 */



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
 * 2016-04-04  
 * @used-by \Dfe\Customer\Plugin\Customer\Model\Address\AbstractAddress::afterValidate()
 * @param AA|CA|QA|OA $a
 * @return StoreInterface|Store
 */
function df_address_store($a) {/** @var Customer|Quote|null $owner */return
	($owner = df_address_owner($a)) ? df_store($owner->getStore()) : null
;}

