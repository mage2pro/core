<?php
/**
 * 2016-12-03 «customer_register_success»: a customer registration event
 * @used-by \Magento\Customer\Controller\Account\CreatePost::execute()
 * https://mage2.pro/t/2357
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Customer/Controller/Account/CreatePost.php#L239-L242
 */
namespace Df\Customer\Observer;
use Df\Customer\Session as Sess;
use Df\Sso\Upgrade\Schema;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
final class RegisterSuccess implements ObserverInterface {
	/**
	 * 2016-12-03
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 */
	function execute(O $o):void {
		$c = df_customer($o['customer']); /** @var Customer $c */
		$s = Sess::s(); /** @var Sess $s */
		if ($s->ssoId()) {
			$c[Schema::fIdC($s->ssoProvider())] = $s->ssoId();
			/**
			 * 2016-12-04
			 * Нельзя использовать здесь @see df_eav_update(),
			 * потому что наше поле не является атрибутом EAV, а является просто полем таблицы customer_entity.
			 */
			$c->save();
		}
		# 2023-01-30
		# «Argument 1 passed to Df\Customer\Session::ssoId() must be of the type string, null given,
		# called in vendor/mage2pro/core/Customer/Observer/RegisterSuccess.php on line 33»:
		# https://github.com/mage2pro/core/issues/202
		$s->ssoId(''); $s->ssoProvider('');
		$s->needConfirm(df_customer_is_need_confirm($c));
	}
}