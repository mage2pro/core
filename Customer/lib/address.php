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
 * 2016-07-27
 * Адрес приобретает тип, только когда используется при оформлении заказа.
 * Пока же адрес просто принадлежит покупателю
 * @see \Magento\Customer\Model\Data\Address
 * @see \Magento\Customer\Api\Data\AddressInterface
 * а не используется в контексте оформления заказа, то такой адрес ещё типа не имеет,
 * и в будущем, в зависимости от контекста,
 * может использоваться и как адрес доставки, и как платёжный адрес.
 * @used-by \Df\Customer\Plugin\Model\Address\AbstractAddress::aroundValidate()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::aroundFormat()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Validator::aroundValidate()
 * @uses \Magento\Quote\Model\Quote\Address::getAddressType()
 * @uses \Magento\Customer\Model\Address::getAddressType()
 * @param AA|CA|QA|OA $a
 */
function df_address_is_billing($a):bool {return df_is_aa($a) ? AA::TYPE_BILLING === $a['address_type'] : (
	df_is_oa($a) ? OA::TYPE_BILLING === $a->getAddressType() : df_error(
		"Invalid address class: «%s».",  get_class($a)
	)
);}

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

/**
 * 2017-04-22
 * @used-by df_address_is_billing()
 * @used-by df_is_address()
 * @param mixed $v
 */
function df_is_aa($v):bool {return $v instanceof AA;}

/**
 * 2017-04-22
 * @used-by df_phone()
 * @param mixed $v
 */
function df_is_address($v):bool {return df_is_aa($v) || df_is_oa($v);}

/**
 * 2017-04-22
 * @used-by df_address_owner()
 * @param mixed $v
 */
function df_is_ca($v):bool {return $v instanceof CA;}

/**
 * 2017-04-22
 * @used-by df_address_is_billing()
 * @used-by df_address_owner()
 * @used-by df_is_address()
 * @param mixed $v
 */
function df_is_oa($v):bool {return $v instanceof OA;}

/**
 * 2017-04-22
 * @used-by df_address_owner()
 * @used-by df_is_address()
 * @param mixed $v
 */
function df_is_qa($v):bool {return $v instanceof QA;}