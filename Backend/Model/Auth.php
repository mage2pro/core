<?php
namespace Df\Backend\Model;
use Magento\Backend\Model\Auth\StorageInterface as IStorage;
use Magento\Security\Model\Plugin\Auth as SecurityPlugin;
// 2016-04-10
class Auth extends \Magento\Backend\Model\Auth {
	/**
	 * 2016-04-10
	 * It is implemented by analogy with @see \Magento\Backend\Model\Auth::login()
	 * https://github.com/magento/magento2/blob/052e789/app/code/Magento/Backend/Model/Auth.php#L137-L182
	 * @param string $email
	 * @throws \Magento\Framework\Exception\AuthenticationException
	 */
	function loginByEmail($email) {
		$this->_initCredentialStorage();
		/** @var \Magento\Backend\Model\Auth\Credential\StorageInterface|\Magento\User\Model\User $user */
		$user = $this->getCredentialStorage();
		$user->{\Df\User\Plugin\Model\User::LOGIN_BY_EMAIL} = true;
		$user->login($email, null);
		if ($user->getId()) {
			/** @var IStorage|\Magento\Backend\Model\Auth\Session $authSession */
			$authSession = $this->getAuthStorage();
			$authSession->setUser($user);
			$authSession->processLogin();
			//$cookieManager->setSensitiveCookie($session->getName(), session_id());
			$_COOKIE[$authSession->getName()] = session_id();
			df_session_manager()->setData(\Magento\Framework\Data\Form\FormKey::FORM_KEY, df_request('form_key'));
			df_dispatch('backend_auth_user_login_success', ['user' => $user]);
			// 2016-04-10
			// Обязательно, иначе авторизация работать не будет.
			// https://mage2.pro/t/1199
			/** @var SecurityPlugin $securityPlugin */
			$securityPlugin = df_o(SecurityPlugin::class);
			$securityPlugin->afterLogin($this);
		}
	}
}