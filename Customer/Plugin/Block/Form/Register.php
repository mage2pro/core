<?php
// 2016-12-03
namespace Df\Customer\Plugin\Block\Form;
use Magento\Customer\Block\Form\Register as Sb;
use Magento\Framework\DataObject as O;
class Register {
	/**
	 * 2016-12-03
	 * Цель плагина — автоматическое заполнение витринной формы регистрации тестовыми данными
	 * при запуске Magento на моём локальном компьютере.
	 * @param Sb $sb
	 * @param O $o
	 * @return O
	 */
	public function afterGetFormData(Sb $sb, O $o) {return $o->setData($o->getData()
		+ df_customer_session()->getDfSsoRegistrationData()
		+ (!df_my_local() ? [] : [
			'dob' => '1982-07-08'
			,'email' => 'test-customer@mage2.pro'
			,'firstname' => 'Test'
			,'lastname' => 'Customer'
			/**
			 * 2016-12-03
			 * Установить пароль здесь нет возможности,
			 * потому что https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Customer/view/frontend/templates/form/register.phtml#L142-L148
			 * не устанавливает значение атрибута «value» для <input type="password">.
			 * Поэтому пароль мы устанавливаем посредством JavaScript:
			 * https://github.com/mage2pro/core/blob/7e7db69/Sso/view/frontend/web/reg-completion.js#L7
			 */
		])
	);}
}