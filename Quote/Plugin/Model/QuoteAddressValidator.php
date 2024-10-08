<?php
namespace Df\Quote\Plugin\Model;
use Magento\Customer\Api\Data\AddressInterface as ICustomerAddress;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Quote\Api\Data\AddressInterface as IQuoteAddress;
use Magento\Quote\Api\Data\CartInterface as ICart;
use Magento\Quote\Model\QuoteAddressValidator as Sb;
# 2021-05-07
final class QuoteAddressValidator {
	/**
	 * 2021-05-07 «Invalid customer address id <…>»: https://github.com/canadasatellite-ca/site/issues/49
	 * @see \Magento\Quote\Model\QuoteAddressValidator::validateForCart():
	 *	public function validateForCart(CartInterface $cart, AddressInterface $address):void {
	 *		$this->doValidate($address, $cart->getCustomerIsGuest() ? null : $cart->getCustomer()->getId());
	 *	}
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Quote/Model/QuoteAddressValidator.php#L119-L131
	 * https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Quote/Model/QuoteAddressValidator.php#L119-L131
	 * @used-by \Magento\Checkout\Model\ShippingInformationManagement::saveAddressInformation():
	 * 		$this->addressValidator->validateForCart($quote, $address);
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Checkout/Model/ShippingInformationManagement.php#L176
	 */
	function aroundValidateForCart(Sb $sb, \Closure $f, ICart $c, IQuoteAddress $a):void {self::doValidate(
		$a, $c->getCustomerId() ?: 0
	);}

	/**
	 * 2021-05-07
	 * «Invalid customer address id <…>»: https://github.com/canadasatellite-ca/site/issues/49
	 * The method duplucates the private method @see \Magento\Quote\Model\QuoteAddressValidator::doValidate():
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Quote/Model/QuoteAddressValidator.php#L55-L102
	 * https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Quote/Model/QuoteAddressValidator.php#L55-L102
	 * @used-by self::aroundValidateForCart()
	 * @throws NSE
	 */
	private static function doValidate(IQuoteAddress $a, int $customerId):void {
		if ($customerId) {
			$customer = df_customer_rep()->getById($customerId);
			if (!$customer->getId()) {
				throw new NSE(__('Invalid customer id %1', $customerId));
			}
		}
		if ($a->getCustomerAddressId()) {
			//Existing address cannot belong to a guest
			if (!$customerId) {
				throw new NSE(__('Invalid customer address id %1', $a->getCustomerAddressId()));
			}
			//Validating address ID
			try {
				df_address_rep()->getById($a->getCustomerAddressId());
			}
			catch (NSE $e) {
				throw new NSE(__('Invalid address id %1', $a->getId()));
			}
			//Finding available customer's addresses
			$applicableAddressIds = array_map(
				function (ICustomerAddress $a) {return $a->getId();}, df_customer_rep()->getById($customerId)->getAddresses()
			);
			if (!in_array($a->getCustomerAddressId(), $applicableAddressIds)) {
				throw new NSE(__('Invalid customer address id %1', $a->getCustomerAddressId()));
			}
		}
	}
}