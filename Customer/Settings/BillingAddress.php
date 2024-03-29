<?php
namespace Df\Customer\Settings;
/**
 * 2016-07-27, 2017-12-13
 * The purpose of this class it to disable the customer's billing address requirement,
 * if a merchant wants so for a particular payment module.
 * @used-by \Df\Customer\Plugin\Model\Address\AbstractAddress
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Validator
 * @see \Df\Payment\Settings::requireBillingAddress()
 */
final class BillingAddress {
	/**     
	 * 2016-07-27
	 * @used-by \Df\Payment\PlaceOrderInternal::_place()
	 */
	static function disable(bool $v = true):void {self::$_stack[]= $v;}
	/**     
	 * 2016-07-27
	 * @used-by \Df\Customer\Plugin\Model\Address\AbstractAddress::aroundValidate()
	 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
	 * @used-by \Df\Sales\Plugin\Model\Order\Address\Validator::aroundValidate()
	 */
	static function disabled():bool {return df_last(self::$_stack);}
	/**      
	 * 2016-07-27
	 * @used-by \Df\Payment\PlaceOrderInternal::_place() 
	 */
	static function restore():void {
		array_pop(self::$_stack);
		df_assert_gt0(count(self::$_stack));
	}
	/**       
	 * 2016-07-27
	 * @used-by self::disable()
	 * @used-by self::disabled()
	 * @used-by self::restore()
	 * @var bool[] 
	 */
	private static $_stack = [false];
}