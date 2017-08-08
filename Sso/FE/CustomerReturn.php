<?php
namespace Df\Sso\FE;
/**
 * 2017-04-23
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * OAuth 2.0 (RFC 6749) допускает 2 способа возвращения пользователя на сайт-клиент после аутентификации:
 * «the authorization server redirects the user-agent back to the client
 * using the redirection URI provided earlier (in the request or during client registration)».
 * https://tools.ietf.org/html/rfc6749#section-4.1
 * Некоторые сервисы аутентификации (например, Amazon), разрешают передавать адрес возвращения
 * непосредственно в запросе на аутентификацию.
 * Другие (как Facebook и Microsoft Azure Active Directory) требуют указания фиксированного адреса
 * возвращения на этапе регистрации приложения, и не допускают динамического изменения этого адреса.
 * Вот для сервисов второй группы и предназначен данный класс: он показывает администратору тот адрес
 * возвращения, который администратор должен указать при регистрации приложения OAuth 2.0
 * @used-by https://github.com/mage2pro/dynamics365/blob/0.0.4/etc/adminhtml/system.xml#L57
 * @used-by https://github.com/mage2pro/facebook-login/blob/1.3.3/etc/adminhtml/system.xml#L50
 */
class CustomerReturn extends \Df\Framework\Form\Element\Url {
	/**
	 * 2017-04-23                                                  
	 * @see https://github.com/mage2pro/dynamics365/blob/0.0.4/etc/adminhtml/system.xml#L57
	 * @override
	 * @see \Df\Framework\Form\Element\Url::url()
	 * @used-by \Df\Framework\Form\Element\Url::messageForOthers()
	 * @return string
	 */
	final protected function url() {
		$isBackend = df_fe_fc_b($this, 'dfWebhook_backend'); /** @var bool $isBackend */
		$route = df_route($this->m(), df_fe_fc($this, 'dfWebhook_suffix'), $isBackend); /** @var string $route */
		return $isBackend ? df_url_backend_ns($route) : df_url_frontend($route);
	}
}