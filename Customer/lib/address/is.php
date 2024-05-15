<?php
use Magento\Customer\Model\Address as CA;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order\Address as OA;

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