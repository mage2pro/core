<?php
namespace Df\Customer;
# 2021-10-26 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
final class Session extends SessionBase {
	/**
	 * 2016-12-04, 2021-10-27
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Sso\Css::isAccConfirmation()
	 * @param bool|string $v [optional]
	 * @return self|bool
	 */
	function needConfirm($v = DF_N) {return df_prop($this, $v, []);}

	/**
	 * 2016-12-03, 2021-10-27
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Sso\CustomerReturn::_execute()
	 * @return self|string
	 */
	function ssoId(string $v = DF_N) {return df_prop($this, $v, []);}

	/**
	 * 2016-12-02, 2021-10-27
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Sso\Css::isRegCompletion()
	 * @used-by \Df\Sso\CustomerReturn::_execute()
	 * @return self|string
	 */
	function ssoProvider(string $v = DF_N) {return df_prop($this, $v, []);}
}