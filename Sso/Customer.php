<?php
namespace Df\Sso;
use Df\Customer\Model\Gender;
/**
 * @see \Dfe\AmazonLogin\Customer
 * @see \Dfe\BlackbaudNetCommunity\Customer
 * @see \Dfe\FacebookLogin\Customer
 */
abstract class Customer extends \Df\Core\O {
	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @see \Dfe\AmazonLogin\Customer::id()
	 * @see \Dfe\BlackbaudNetCommunity\Customer::id()
	 * @see \Dfe\FacebookLogin\Customer::id()
	 */
	abstract function id():string;

	/**
	 * 2016-06-04
	 * @return \DateTime|null
	 */
	final function dob() {return dfc($this, function() {/** @var \DateTime|null $r */
		if (!($r = $this->_dob()) && df_customer_att_is_required('dob')) {
			$r = new \DateTime;
			$r->setDate(1900, 1, 1);
		}
		return $r;
	});}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::customerData()
	 * @see \Dfe\AmazonLogin\Customer::email()
	 * @see \Dfe\FacebookLogin\Customer::email()
	 */
	function email():string {return '';}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @see \Dfe\FacebookLogin\Customer::gender()
	 * @return int
	 */
	function gender():int {return Gender::UNKNOWN;}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @see \Dfe\AmazonLogin\Customer::nameFirst()
	 * @see \Dfe\FacebookLogin\Customer::nameFirst()
	 * @return string|null
	 */
	function nameFirst() {return null;}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @see \Dfe\AmazonLogin\Customer::nameLast()
	 * @see \Dfe\FacebookLogin\Customer::nameLast()
	 * @return string|null
	 */
	function nameLast() {return null;}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @see \Dfe\FacebookLogin\Customer::nameMiddle()
	 * @return string|null
	 */
	function nameMiddle() {return '';}

	/**
	 * 2016-06-04
	 * 2016-06-05
	 * По крайней мере, для Amazon надо брать последние символы идентификатора,
	 * потому что первые одинаковы для всех: «amzn1.account.AGM6GZJB6GO42REKZDL33HG7GEJA»
	 * @used-by CustomerReturn::register()
	 */
	final function password():string {return substr($this->id(), -8);}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::c()
	 * @see \Dfe\AmazonLogin\Customer::validate()
	 * @see \Dfe\BlackbaudNetCommunity\Customer::validate()
	 * @throws \Exception
	 */
	function validate():void {}

	/**
	 * 2016-06-04
	 * @used-by self::dob()
	 * @see \Dfe\FacebookLogin\Customer::_dob()
	 * @return \DateTime|null
	 */
	protected function _dob() {return null;}
}