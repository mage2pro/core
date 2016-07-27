<?php
use Magento\Customer\Api\Data\AddressInterface as ICDA;
use Magento\Customer\Model\Address as CA;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Data\Address as CDA;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
/**
 * 2016-07-27
 * Адрес приобретает тип, только когда используется при оформлении заказа.
 * Пока же адрес просто принадлежит покупателю
 * @see \Magento\Customer\Model\Data\Address
 * @see \Magento\Customer\Api\Data\AddressInterface
 * а не используется в контексте оформления заказа, то такой адрес ещё типа не имеет,
 * и в будущем, в зависимости от контекста,
 * может использоваться и как адрес доставки, и как платёжный адрес.
 *
 * @uses \Magento\Quote\Model\Quote\Address::getAddressType()
 * @uses \Magento\Customer\Model\Address::getAddressType()
 * @param AA|CA|QA|OA $a
 * @return bool
 */
function df_address_is_billing($a) {
	return $a instanceof AA ? AA::TYPE_BILLING === $a['address_type'] : (
		$a instanceof OA ? OA::TYPE_BILLING === $a->getAddressType() :
			df_error("Invalid address class: «%s».",  get_class($a))
	);
}

/**
 * 2016-04-04
 * @param AA|CA|QA|OA $a
 * @return Customer|Quote|Order|null
 */
function df_address_owner($a) {
	return $a instanceof CA ? $a->getCustomer() : (
		$a instanceof QA ? $a->getQuote() : (
			$a instanceof OA ? $a->getOrder() : null
		)
	);
}

/**
 * 2016-04-05
 * @return AddressRegistry
 */
function df_address_registry() {return df_o(AddressRegistry::class);}

/**
 * 2016-04-04
 * @param AA|CA|QA|OA $a
 * @return StoreInterface|Store
 */
function df_address_store($a) {
	/** @var Customer|Quote|null $owner */
	$owner = df_address_owner($a);
	return $owner ? df_store($owner->getStore()) : null;
}

