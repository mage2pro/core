<?php
namespace Df\Backend\Model;
class Auth extends \Magento\Backend\Model\Auth {
	/**
	 * @param string $email
	 * @return void
	 * @throws \Magento\Framework\Exception\AuthenticationException
	 */
	public function loginByEmail($email) {
		$this->_initCredentialStorage();
		/** @var \Magento\Backend\Model\Auth\Credential\StorageInterface|\Magento\User\Model\User $user */
		$user = $this->getCredentialStorage();
		$user->{\Df\User\Model\UserPlugin::LOGIN_BY_EMAIL} = true;
		$user->login($email, null);
		if ($user->getId()) {
			/** @var \Magento\Backend\Model\Auth\StorageInterface|\Magento\Backend\Model\Auth\Session $authSession */
			$authSession = $this->getAuthStorage();
			$authSession->setUser($user);
			$authSession->processLogin();
			//$cookieManager->setSensitiveCookie($session->getName(), session_id());
			$_COOKIE[$authSession->getName()] = session_id();
			/** @var \Magento\Framework\Session\SessionManagerInterface|\Magento\Backend\Model\Session $session */
			$session = df_o('Magento\Framework\Session\SessionManagerInterface');
			$session->setData(\Magento\Framework\Data\Form\FormKey::FORM_KEY, df_request('form_key'));
			$this->_eventManager->dispatch('backend_auth_user_login_success', ['user' => $user]);
		}
	}
}

