<?php
namespace Df\Core\Observer;
use Df\Core\GlobalSingletonDestructor as D;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
final class ControllerFrontSendResponseAfter implements ObserverInterface {
	/**
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 */
	function execute(O $o) {D::s()->process();}
}