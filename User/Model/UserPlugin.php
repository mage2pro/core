<?php
namespace Df\User\Model;
class UserPlugin {
	/**
	 * @param \Magento\Framework\Model\Context $context
	 */
	public function __construct(\Magento\Framework\Model\Context $context) {
		$this->_eventManager = $context->getEventDispatcher();
	}

	/**
	 * @param \Magento\User\Model\User $subject
	 * @param \Closure $proceed
	 * @param string $username
	 * @param string $password
	 * @return bool
	 * @throws \Magento\Framework\Exception\AuthenticationException
	 */
	public function aroundAuthenticate(
		\Magento\User\Model\User $subject
		, \Closure $proceed
		, $username
		, $password
	) {
		/** @var bool $loginByEmail */
		$loginByEmail = df_ok($subject, self::LOGIN_BY_EMAIL);
		unset($subject->{self::LOGIN_BY_EMAIL});
		/** @var bool $result */
		$result = false;
		if ($loginByEmail) {
			/** @var array(string => mixed)|false $data */
			$data = \Df\User\Model\ResourceModel\User::s()->loadByEmail($username);
			if ($data) {
				$subject->setData($data);
				$result = true;
				$this->_eventManager->dispatch('admin_user_authenticate_after', [
					'username' => $username
					, 'password' => null
					, 'user' => $subject
					, 'result' => $result
				]);
			}
		}
		return $result ? $result : $proceed($username, $password);
	}

	const LOGIN_BY_EMAIL = 'login_by_email';
}