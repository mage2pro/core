<?php
namespace Df\Payment;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface as IGuest;
use Magento\Checkout\Api\PaymentInformationManagementInterface as IRegistered;
use Magento\Checkout\Model\GuestPaymentInformationManagement as Guest;
use Magento\Checkout\Model\PaymentInformationManagement as Registered;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\AddressInterface as IAddress;
use Magento\Quote\Api\GuestCartManagementInterface as IGuestQM;
use Magento\Quote\Api\Data\PaymentInterface as IPayment;
use Magento\Quote\Api\CartManagementInterface as IQM;
use Magento\Quote\Model\GuestCart\GuestCartManagement as GuestQM;
use Magento\Quote\Model\QuoteManagement as QM;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
class PlaceOrder {
	/**
	 * 2016-05-04
	 * @param string $cartId
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
		return $this->place(IGuestQM::class, $cartId);
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
		return $this->place(IQM::class, $cartId);
	}

	/**
	 * 2016-07-16
	 * @param string $qmClass
	 * @param int $cartId
	 * @return mixed|null
	 * @throws CouldNotSaveException
	 */
	private function place($qmClass, $cartId) {
		/** @var IQM|QM|IGuestQM|GuestQM $qm */
		$qm = df_o($qmClass);
		/** @var mixed $result */
		try {
			/** @var int $orderId */
			$orderId = $qm->placeOrder($cartId);
			$result = df_order($orderId)->getPayment()->getAdditionalInformation(self::DATA);
		}
		catch (\Exception $e) {
			/** @var \Exception|null $prev */
			$prev = $e->getPrevious();
			df_log($prev ?: $e);
			/** @var array(string|Phrase) $messageA */
			$messageA[]= __('Sorry, the payment attempt is failed.');
			if ($prev) {
				/** @var string $eMessage */
				$eMessage = df_ets($prev);
				if ($eMessage) {
					$messageA[]= __("The payment service's message is «<b>%1</b>».", $eMessage);
				}
			}
			$messageA[]= __('Please try again, or try another payment method.');
			throw new CouldNotSaveException(__(implode('<br/>', $messageA)), $e);
		}
		return $result;
	}

	/**
	 * 2016-07-01
	 * @used-by \Df\Payment\PlaceOrder::response()
	 * @used-by \Dfe\AllPay\Method::getConfigPaymentAction()
	 * @used-by \Dfe\CheckoutCom\Method::redirectUrl()
	 */
	const DATA = 'df_data';
}


