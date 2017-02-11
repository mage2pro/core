<?php
namespace Df\Core\Observer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
class ControllerFrontSendResponseAfter implements ObserverInterface {
	/**
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 * @return void
	 */
	function execute(O $o) {\Df\Core\GlobalSingletonDestructor::s()->process();}
}