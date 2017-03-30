<?php
namespace Df\Customer\Observer\CopyFieldset;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
/**
 * 2016-08-22
 * Событие: core_copy_fieldset_order_address_to_customer
 * @see \Magento\Framework\DataObject\Copy::dispatchCopyFieldSetEvent()
 * https://mage2.pro/t/1975    
 * 
 * 2017-02-09
 * Этот обработчик используется нами в сценарии сохранения наших нестандартных данных для покупателя.
 * На сегодняшний день мы сохраняем для покупателя его идентификаторы в различных платёжных системах
 * (Stripe, Omise, Paymill), чтобы при повторных платежах покупатель мог использовать
 * ранее уже использованные им банковские карты без повторного ввода их реквизитов.
 * Эти нестандартные данные для покупателя были записаны в сессию в функции @see df_ci_save()
 * в том случае, когда покупатель на момент оформления заказа не был ещё зарегистрирован,
 * и поэтому у нас не было возможности записать эти данные прямо в покупателя.
 */
final class OrderAddressToCustomer implements ObserverInterface {
	/**
	 * 2016-08-22
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 */
	function execute(O $o) {df_ci_add($o['target'], df_checkout_session()->getDfCustomer() ?: []);}
}

