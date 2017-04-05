<?php
namespace Df\Payment;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface as IGuest;
use Magento\Checkout\Api\PaymentInformationManagementInterface as IRegistered;
use Magento\Checkout\Model\GuestPaymentInformationManagement as Guest;
use Magento\Checkout\Model\PaymentInformationManagement as Registered;
use Magento\Framework\Exception\CouldNotSaveException as CouldNotSave;
use Magento\Quote\Api\Data\AddressInterface as IQA;
use Magento\Quote\Api\Data\PaymentInterface as IQP;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Quote\Model\Quote\Payment as QP;
class PlaceOrder {
	/**
	 * 2016-05-04
	 * 2017-04-04
	 * The arguments are arrived from Df_Checkout/js/action/place-order:
	 * https://github.com/mage2pro/core/blob/2.4.24/Checkout/view/frontend/web/js/action/place-order.js#L64-L66
	 * @param string $cartId
	 * For the quest customers $cartId is a string like «63b25f081bfb8e4594725d8a58b012f7».
	 * @param string $email
	 * @param IQP|QP $qp
	 * @param IQA|QA|null $ba
	 * 2017-04-04 Важно возвращать именно string: @see dfw_encode()
	 * @return string
	 * @throws CouldNotSave
	 */
	function guest($cartId, $email, IQP $qp, IQA $ba = null) {return $this->p(
		true, $cartId, $email, $qp, $ba
	);}

	/**
	 * 2016-05-04
	 * 2017-04-04
	 * @param int $cartId
	 * @param IQP|QP $qp
	 * @param IQA|QA|null $ba
	 * @return string
	 * @throws CouldNotSave
	 */
	function registered($cartId, IQP $qp, IQA $ba = null) {return $this->p(false, $cartId, $qp, $ba);}

	/**
	 * 2017-04-05
	 * The arguments are arrived from Df_Checkout/js/action/place-order:
	 * https://github.com/mage2pro/core/blob/2.4.24/Checkout/view/frontend/web/js/action/place-order.js#L64-L66
	 * @param bool $isGuest
	 * @param int|string $cartId
	 * @param mixed ...$args
	 * 2017-04-04 Важно возвращать именно string: @see dfw_encode()
	 * @return string
	 * @throws CouldNotSave
	 */
	private function p($isGuest, $cartId, ...$args) {
		/** @var IGuest|Guest|IRegistered|Registered $saver */
		$saver = df_o($isGuest ? IGuest::class : IRegistered::class);
		$saver->savePaymentInformation($cartId, ...$args);
		return PlaceOrderInternal::p($cartId, $isGuest);
	}
}