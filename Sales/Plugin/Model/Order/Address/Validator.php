<?php
namespace Df\Sales\Plugin\Model\Order\Address;
use Df\Customer\Settings\BillingAddress as S;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Validator as Sb;
# 2023-08-06
# "Prevent interceptors generation for the plugins extended from interceptable classes":
# https://github.com/mage2pro/core/issues/327
# 2023-12-31
# "Declare as `final` the final classes implemented `\Magento\Framework\ObjectManager\NoninterceptableInterface`"
# https://github.com/mage2pro/core/issues/345
final class Validator extends Sb implements \Magento\Framework\ObjectManager\NoninterceptableInterface {
	/** 2016-04-05 */
	function __construct() {}

	/**
	 * 2016-07-27
	 * Цель плагина — добавление возможности отключения необходимости платёжного адреса.
	 * Это будет использоваться моими платёжными модулями.
	 * Помимо этого плагина для данной функциональности нужны ещё 2:
	 * 		1) @see \Df\Customer\Plugin\Model\Address\AbstractAddress
	 * 		2) @see \Df\Customer\Plugin\Model\ResourceModel\AddressRepository
	 * @see \Magento\Sales\Model\Order\Address\Validator::validate()
	 * @return string[]
	 */
	function aroundValidate(Sb $sb, \Closure $f, Address $a):array {return
		S::disabled() && df_address_is_billing($a) ? [] : $f($a)
	;}
}