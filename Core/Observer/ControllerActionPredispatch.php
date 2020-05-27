<?php
namespace Df\Core\Observer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
final class ControllerActionPredispatch implements ObserverInterface {
	/**
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @see \Magento\Framework\App\Action\Action::dispatch()
	 * 		$eventParameters = ['controller_action' => $this, 'request' => $request];
	 *		$this->_eventManager->dispatch('controller_action_predispatch', $eventParameters);
	 * https://github.com/magento/magento2/blob/2.3.5-p1/lib/internal/Magento/Framework/App/Action/Action.php#L96-L102
	 * @param O $o
	 */
	function execute(O $o) {df_state()->controllerSet($o['controller_action']);}
}