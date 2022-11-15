<?php
namespace Df\User\Plugin\Model;
use Df\User\Model\ResourceModel\User as Resource;
use Magento\User\Model\User as Sb;
class User {
	/** @see \Magento\User\Model\User::authenticate() */
	function aroundAuthenticate(Sb $sb, \Closure $f, string $username, string $password):bool {
		$loginByEmail = df_prop_k($sb, self::LOGIN_BY_EMAIL); /** @var bool $loginByEmail */
		df_prop_k($sb, self::LOGIN_BY_EMAIL, false);
		$r = false; /** @var bool $r */
		if ($loginByEmail) {
			/**
			 * 2016-04-10
			 * It is implemented by analogy with @see \Magento\User\Model\User::loadByUsername()
			 * https://github.com/magento/magento2/blob/052e789/app/code/Magento/User/Model/User.php#L606-L619
			 * @var array(string => mixed)|false $data
			 */
			if ($data = Resource::s()->loadByEmail($username)) {
				$sb->setData($data);
				$r = true;
				df_dispatch('admin_user_authenticate_after', [
					'username' => $username, 'password' => null, 'user' => $sb, 'result' => $r
				]);
			}
		}
		return $r ?: $f($username, $password);
	}

	/**
	 * @used-by self::aroundAuthenticate()
	 * @used-by \Df\Backend\Model\Auth::loginByEmail()
	 */
	const LOGIN_BY_EMAIL = 'login_by_email';
}