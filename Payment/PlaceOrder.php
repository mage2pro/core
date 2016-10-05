<?php
namespace Df\Payment;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface as IGuest;
use Magento\Checkout\Api\PaymentInformationManagementInterface as IRegistered;
use Magento\Checkout\Model\GuestPaymentInformationManagement as Guest;
use Magento\Checkout\Model\PaymentInformationManagement as Registered;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\Data\AddressInterface as IAddress;
use Magento\Quote\Api\Data\PaymentInterface as IPayment;
class PlaceOrder {
	/**
	 * 2016-05-04
	 * @param string $cartId
	 * Для анонимных покупателей $cartId — это строка вида «63b25f081bfb8e4594725d8a58b012f7»
	 * @param string $email
	 * @param IPayment $paymentMethod
	 * @param IAddress|null $billingAddress
	 * @return mixed
	 * @throws CouldNotSaveException
	 */
	public function guest($cartId, $email, IPayment $paymentMethod, IAddress $billingAddress = null) {
		/** @var IGuest|Guest $iGuest */
		$iGuest = df_o(IGuest::class);
		$iGuest->savePaymentInformation($cartId, $email, $paymentMethod, $billingAddress);
		return PlaceOrderInternal::p($cartId, true);
	}

	/**
	 * 2016-05-04
	 * @param int $cartId
	 * @param IPayment $paymentMethod
	 * @param IAddress|null $billingAddress
	 * @return mixed
	 * @throws CouldNotSaveException
	 */
	public function registered($cartId, IPayment $paymentMethod, IAddress $billingAddress = null) {
		/** @var IRegistered|Registered $iRegistered */
		$iRegistered = df_o(IRegistered::class);
		$iRegistered->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
		return PlaceOrderInternal::p($cartId, false);
	}

	/**
	 * 2016-07-01
	 * @used-by \Df\Payment\PlaceOrderInternal::_place()
	 * @used-by \Dfe\AllPay\Method::getConfigPaymentAction()
	 * @used-by \Dfe\CheckoutCom\Method::redirectUrl()
	 */
	const DATA = 'df_data';
}