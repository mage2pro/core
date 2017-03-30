<?php
namespace Df\Customer\Settings;
/**
 * 2016-07-27
 * Цель класса — добавление возможности отключения необходимости платёжного адреса.
 * Это будет использоваться моими платёжными модулями.
 * @used-by \Df\Customer\Plugin\Model\Address\AbstractAddress
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Validator
 * @see \Df\Payment\Settings::requireBillingAddress()
 */
class BillingAddress {
	/**     
	 * 2016-07-27
	 * @param bool $v [optional]
	 */
	static function disable($v = true) {self::$_stack[]= $v;}
	/**     
	 * 2016-07-27
	 * @return bool 
	 */
	static function disabled() {return df_last(self::$_stack);}
	/**      
	 * 2016-07-27
	 * @used-by \Df\Payment\PlaceOrderInternal::_place() 
	 */
	static function restore() {
		array_pop(self::$_stack);
		df_assert_gt0(count(self::$_stack));
	}
	/**       
	 * 2016-07-27
	 * @var bool[] 
	 */
	private static $_stack = [false];
}