<?php
/**
 * 2016-12-03
 * «customer_register_success»: a customer registration event
 * @used-by \Magento\Customer\Controller\Account\CreatePost::execute()
 * https://mage2.pro/t/2357
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Customer/Controller/Account/CreatePost.php#L239-L242
 */
namespace Df\Customer\Observer;
use Df\Customer\Model\Session;
use Df\Sso\Install\Schema;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
class RegisterSuccess implements ObserverInterface {
	/**
	 * 2016-12-03
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 * @return void
	 */
	public function execute(O $o) {
		/** @var Session $s */
		$s = df_customer_session();
		if ($s->getDfSsoId()) {
			df_eav_update($o['customer'], Schema::fIdC($s->getDfSsoProvider()), $s->getDfSsoId());
		}
		$s->unsDfSsoId()->unsDfSsoProvider()->unsDfSsoRegistrationData();
	}
}