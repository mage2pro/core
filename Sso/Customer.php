<?php
namespace Df\Sso;
use Df\Customer\Model\Gender;
abstract class Customer extends \Df\Core\O {
	/**
	 * 2016-06-04
	 * @used-by ReturnT::customerData()
	 * @return string|null
	 */
	abstract public function email();

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Core\O::id()
	 * @used-by ReturnT::register()
	 * @return string
	 */
	abstract public function id();

	/**
	 * 2016-06-04
	 * @used-by ReturnT::register()
	 * @return string
	 */
	abstract public function nameFirst();

	/**
	 * 2016-06-04
	 * @used-by ReturnT::register()
	 * @return string
	 */
	abstract public function nameLast();

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
	 * @see \Df\Customer\Model\Gender
	 * @used-by ReturnT::register()
	 * @return int
	 */
	public function gender() {return Gender::UNKNOWN;}

	/**
	 * 2016-06-04
	 * @used-by ReturnT::register()
	 * @return string|null
	 */
	public function nameMiddle() {return '';}

	/**
	 * 2016-06-04
	 * 2016-06-05
	 * По крайней мере, для Amazon надо брать последние символы идентификатора,
	 * потому что первые одинаковы для всех: «amzn1.account.AGM6GZJB6GO42REKZDL33HG7GEJA»
	 * @used-by ReturnT::register()
	 * @return string
	 */
	public function password() {return substr($this->id(), -8);}

	/**
	 * 2016-06-04
	 * @used-by ReturnT::c()
	 * @return void
	 * @throws \Exception
	 */
	public function validate() {}

	/**
	 * 2016-06-04
	 * @used-by dob()
	 * @return \DateTime|null
	 */
	protected function _dob() {return null;}
}