<?php
namespace Df\Customer\External;
use Df\Customer\Model\Gender;
abstract class Customer extends \Df\Core\O {
	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\External\Customer::id()
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return string
	 */
	abstract public function id();

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return string
	 */
	abstract public function nameFirst();

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return string
	 */
	abstract public function nameLast();

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\Customer::email()
	 * @return string|null
	 */
	abstract protected function _email();

	/**
	 * 2016-06-04
	 * @return \DateTime|null
	 */
	public function dob() {return dfc($this, function() {
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
	 * @return string
	 */
	public function email() {return dfc($this, function() {return
		$this->_email() ?: df_next_increment('customer_entity') . '@none.com'
	;});}

	/**
	 * 2016-06-04
	 * @see \Df\Customer\Model\Gender
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return int
	 */
	public function gender() {return Gender::UNKNOWN;}

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return string|null
	 */
	public function nameMiddle() {return '';}

	/**
	 * 2016-06-04
	 * 2016-06-05
	 * По крайней мере, для Amazon надо брать последние символы идентификатора,
	 * потому что первые одинаковы для всех: «amzn1.account.AGM6GZJB6GO42REKZDL33HG7GEJA»
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return string
	 */
	public function password() {return substr($this->id(), -8);}

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\ReturnT::c()
	 * @return void
	 * @throws \Exception
	 */
	public function validate() {}

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\Customer::dob()
	 * @return \DateTime|null
	 */
	protected function _dob() {return null;}
}