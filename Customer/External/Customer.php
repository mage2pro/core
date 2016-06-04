<?php
namespace Df\Customer\External;
abstract class Customer extends \Df\Core\O {
	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\Customer::dob()
	 * @return \DateTime|null
	 */
	abstract protected function _dob();

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\Customer::email()
	 * @return string|null
	 */
	abstract protected function _email();

	/**
	 * 2016-06-04
	 * @see \Df\Customer\Model\Gender
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return int
	 */
	abstract public function gender();

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
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return string|null
	 */
	abstract public function nameMiddle();

	/**
	 * 2016-06-04
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return string
	 */
	abstract public function password();

	/**
	 * 2016-06-04
	 * @return \DateTime|null
	 */
	public function dob() {
		if (!isset($this->{__METHOD__})) {
			/** @var \DateTime|null $result */
			$result = $this->_dob();
			if (!$result && df_is_customer_attribute_required('dob')) {
				$result = new \DateTime;
				$result->setDate(1900, 1, 1);
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-06-04
	 * @return string
	 */
	public function email() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->_email() ?: df_next_increment('customer_entity') . '@none.com';
		}
		return $this->{__METHOD__};
	}
}