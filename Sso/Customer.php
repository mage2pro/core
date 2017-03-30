<?php
namespace Df\Sso;
use Df\Customer\Model\Gender;
abstract class Customer extends \Df\Core\O {
	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Core\O::id()
	 * @used-by CustomerReturn::register()
	 * @return string|int
	 */
	abstract function id();

	/**
	 * 2016-06-04
	 * @return \DateTime|null
	 */
	function dob() {return dfc($this, function() {
		/** @var \DateTime|null $result */
		$result = $this->_dob();
		if (!$result && df_is_customer_attribute_required('dob')) {
			$result = new \DateTime;
			$result->setDate(1900, 1, 1);
		}
		return $result;
	});}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::customerData()
	 * @return string|null
	 */
	function email() {return null;}

	/**
	 * 2016-06-04
	 * @see \Df\Customer\Model\Gender
	 * @used-by CustomerReturn::register()
	 * @return int
	 */
	function gender() {return Gender::UNKNOWN;}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @return string|null
	 */
	function nameFirst() {return null;}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @return string
	 */
	function nameLast() {return null;}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::register()
	 * @return string|null
	 */
	function nameMiddle() {return '';}

	/**
	 * 2016-06-04
	 * 2016-06-05
	 * По крайней мере, для Amazon надо брать последние символы идентификатора,
	 * потому что первые одинаковы для всех: «amzn1.account.AGM6GZJB6GO42REKZDL33HG7GEJA»
	 * @used-by CustomerReturn::register()
	 * @return string
	 */
	function password() {return substr($this->id(), -8);}

	/**
	 * 2016-06-04
	 * @used-by CustomerReturn::c()
	 * @throws \Exception
	 */
	function validate() {}

	/**
	 * 2016-06-04
	 * @used-by dob()
	 * @return \DateTime|null
	 */
	protected function _dob() {return null;}
}