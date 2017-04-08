<?php
namespace Df\Customer\Plugin\Model\ResourceModel;
use Df\Customer\Settings\BillingAddress as S;
use Magento\Customer\Api\Data\AddressInterface as AI;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Data\Address as CDA;
use Magento\Customer\Model\ResourceModel\AddressRepository as Sb;
use Magento\Framework\Exception\InputException;
final class AddressRepository {
	/**
	 * 2016-07-27
	 * Цель плагина — добавление возможности отключения необходимости платёжного адреса.
	 * Это будет использоваться моими платёжными модулями.
	 * Помимо этого плагина для данной функциональности нужны ещё 2:
	 * @see \Df\Customer\Plugin\Model\Address\AbstractAddress
	 * @see \Df\Sales\Plugin\Model\Order\Address\Validator
	 *
	 * @see \Magento\Customer\Model\ResourceModel\AddressRepository::save()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param AI|CDA $address
	 * @return AI
	 * @throws InputException
	 */
	function aroundSave(Sb $sb, \Closure $f, AI $address) {
		/** @var AI $result */
		/**
		 * 2016-07-27
		 * Адрес приобретает тип, только когда используется при оформлении заказа.
		 * Пока же адрес просто принадлежит покупателю
		 * @see \Magento\Customer\Model\Data\Address
		 * @see \Magento\Customer\Api\Data\AddressInterface
		 * а не используется в контексте оформления заказа, то такой адрес ещё типа не имеет,
		 * и в будущем, в зависимости от контекста,
		 * может использоваться и как адрес доставки, и как платёжный адрес.
		 *
		 * По этой причине мы не вызываем здесь @see df_address_is_billing()
		 * В то же время, мы попадаем сюда при оформлении заказа,
		 * поэтому мы не можем проводить валидацию адреса,
		 * если необходимость платёжного адреса отключена администратором.
		 * Поэтому ветка S::disabled() нужна.
		 */
		if (!S::disabled()) {
			$result = $f($address);
		}
		else {
			/** @var Customer $customer */
			$customer = df_customer($address->getCustomerId());
			/** @var Address $addressM */
			$addressM = null;
			if ($address->getId()) {
				$addressM = df_address_registry()->retrieve($address->getId());
			}
			if ($addressM) {
				$addressM->updateData($address);
			}
			else {
				$addressM = df_new_om(Address::class);
				$addressM->updateData($address);
				$addressM->setCustomer($customer);
			}
			// 2016-07-27
			// Вот здесь в ядре валидация, а мы её пропускаем.
			$addressM->save();
			// Clean up the customer registry since the Address save has side effect on customer:
			// \Magento\Customer\Model\ResourceModel\Address::_afterSave
			df_customer_registry()->remove($address->getCustomerId());
			df_address_registry()->push($addressM);
			$customer->getAddressesCollection()->clear();
			$result = $addressM->getDataModel();
		}
		return $result;
	}
}