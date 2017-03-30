<?php
namespace Df\User\Plugin\Model;
use Df\User\Model\ResourceModel\User as Resource;
use Magento\User\Model\User as Sb;
class User {
	/**
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $username
	 * @param string $password
	 * @return bool
	 * @throws \Magento\Framework\Exception\AuthenticationException
	 */
	function aroundAuthenticate(Sb $sb, \Closure $f, $username, $password) {
		/** @var bool $loginByEmail */
		$loginByEmail = dfo($sb, self::LOGIN_BY_EMAIL);
		unset($sb->{self::LOGIN_BY_EMAIL});
		/** @var bool $result */
		$result = false;
		if ($loginByEmail) {
			/**
			 * 2016-04-10
			 * It is implemented by analogy with @see \Magento\User\Model\User::loadByUsername()
			 * https://github.com/magento/magento2/blob/052e789/app/code/Magento/User/Model/User.php#L606-L619
			 */
			/** @var array(string => mixed)|false $data */
			$data = Resource::s()->loadByEmail($username);
			if ($data) {
				$sb->setData($data);
				$result = true;
				df_dispatch('admin_user_authenticate_after', [
					'username' => $username, 'password' => null, 'user' => $sb, 'result' => $result
				]);
			}
		}
		return $result ? $result : $f($username, $password);
	}

	/** @used-by \Df\Backend\Model\Auth::loginByEmail() */
	const LOGIN_BY_EMAIL = 'login_by_email';
}