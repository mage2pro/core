<?php
namespace Df\Core\Observer;
use Magento\Framework\Event\ObserverInterface;
class ControllerFrontSendResponseAfter implements ObserverInterface {
	/**
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param \Magento\Framework\Event\Observer $o
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $o) {
		\Df\Core\GlobalSingletonDestructor::s()->process();
	}
}