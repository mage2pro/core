<?php
namespace Df\Framework\Mail;
use Df\Framework\Plugin\Mail\TransportInterfaceFactory as P;
use Magento\Framework\Event\Observer as Ob;
use Magento\Framework\Event\ObserverInterface as IOb;
/**
 * 2018-01-28
 * @see \Dfe\Mailgun\Observer
 */
abstract class TransportObserver implements IOb {
	/**
	 * 2018-01-28
	 * @override
	 * @see IOb::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 */
	final function execute(Ob $ob):void {
		if (dfs($this)->enable()) {
			$ob[P::CONTAINER][P::K_TRANSPORT] = df_con($this, 'Transport');
		}
	}
}