<?php
namespace Df\User\Plugin\Model;
use Magento\User\Model\User as Sb;
class User {
	/**
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @param string $username
	 * @param string $password
	 * @return bool
	 * @throws \Magento\Framework\Exception\AuthenticationException
	 */
	public function aroundAuthenticate(Sb $sb, \Closure $proceed, $username, $password) {
		/** @var bool $loginByEmail */
		$loginByEmail = df_ok($sb, self::LOGIN_BY_EMAIL);
		unset($sb->{self::LOGIN_BY_EMAIL});
		/** @var bool $result */
		$result = false;
		if ($loginByEmail) {
			/** @var array(string => mixed)|false $data */
			$data = \Df\User\Model\ResourceModel\User::s()->loadByEmail($username);
			if ($data) {
				$sb->setData($data);
				$result = true;
				df_dispatch('admin_user_authenticate_after', [
					'username' => $username, 'password' => null, 'user' => $sb, 'result' => $result
				]);
			}
		}
		return $result ? $result : $proceed($username, $password);
	}

	/** @used-by \Df\Backend\Model\Auth::loginByEmail() */
	const LOGIN_BY_EMAIL = 'login_by_email';
}