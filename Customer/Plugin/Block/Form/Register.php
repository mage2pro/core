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
	public function afterGetFormData(Sb $sb, O $o) {return
		!df_my_local() || $o->getData() ? $o : $o->setData([
			'dob' => '1982-07-08'
			,'email' => 'test-customer@mage2.pro'
			,'firstname' => 'Test'
			,'lastname' => 'Customer'
			// 2016-12-03
			// К сожалению, установить пароль здесь нет возможности.
			// Минимальная длина — 8 символов.
			//,'password' => '11111111'
			//,'password_confirmation' => '11111111'
		])
	;}
}