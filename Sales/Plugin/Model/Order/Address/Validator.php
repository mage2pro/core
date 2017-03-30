<?php
namespace Df\Sales\Plugin\Model\Order\Address;
use Df\Customer\Settings\BillingAddress as S;
use Magento\Customer\Model\Customer;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Validator as Sb;
use Magento\Store\Model\Store;
class Validator extends Sb {
	/** 2016-04-05 */
	function __construct() {}

	/**
	 * 2016-07-27
	 * Цель плагина — добавление возможности отключения необходимости платёжного адреса.
	 * Это будет использоваться моими платёжными модулями.
	 * Помимо этого плагина для данной функциональности нужны ещё 2:
	 * @see \Df\Customer\Plugin\Model\Address\AbstractAddress
	 * @see \Df\Customer\Plugin\Model\ResourceModel\AddressRepository
	 *
	 * @see \Magento\Sales\Model\Order\Address\Validator::validate()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param Address $a
	 * @return string[]
	 */
	function aroundValidate(Sb $sb, \Closure $f, Address $a) {return
		S::disabled() && df_address_is_billing($a) ? [] : $f($a)
	;}
}