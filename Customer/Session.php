<?php
namespace Df\Customer;
# 2021-10-26 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
final class Session extends \Df\Core\Session {
	/**
	 * 2016-12-04, 2021-10-27
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Sso\Css::isAccConfirmation()
	 * @param bool|string $v [optional]
	 * @return $this|bool
	 */
	function needConfirm($v = DF_N) {return df_prop($this, $v, []);}

	/**
	 * 2016-12-03, 2021-10-27
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Sso\CustomerReturn::_execute()
	 * @param string $v [optional]
	 * @return $this|string
	 */
	function ssoId($v = DF_N) {return df_prop($this, $v, []);}

	/**
	 * 2016-12-02, 2021-10-27
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Sso\Css::isRegCompletion()
	 * @used-by \Df\Sso\CustomerReturn::_execute()
	 * @param string $v [optional]
	 * @return $this|string
	 */
	function ssoProvider($v = DF_N) {return df_prop($this, $v, []);}

	/**
	 * 2016-12-03, 2021-10-27
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Customer\Plugin\Block\Form\Register::afterGetFormData()
	 * @used-by \Df\Sso\CustomerReturn::_execute()
	 * @param array(string => mixed)|string $v [optional]
	 * @return $this|array(string => mixed)
	 */
	function ssoRegistrationData($v = DF_N) {return df_prop($this, $v, []);}

	/**
	 * 2021-10-26
	 * @override
	 * @see \Df\Core\Session::c()
	 * @used-by \Df\Core\Session::__construct()
	 * @return string
	 */
	protected function c() {return \Magento\Customer\Model\Session\Storage::class;}
}