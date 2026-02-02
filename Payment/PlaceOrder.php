<?php
namespace Df\Payment;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface as IGuest;
use Magento\Checkout\Api\PaymentInformationManagementInterface as IRegistered;
use Magento\Checkout\Model\GuestPaymentInformationManagement as Guest;
use Magento\Checkout\Model\PaymentInformationManagement as Registered;
use Magento\Framework\Exception\CouldNotSaveException as CouldNotSave;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Quote\Api\Data\AddressInterface as IQA;
use Magento\Quote\Api\Data\PaymentInterface as IQP;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Quote\Model\Quote\Payment as QP;
/**
 * 2016-05-04
 * @used-by https://github.com/mage2pro/core/blob/2.12.5/Payment/etc/webapi.xml#L8
 * 		<service class='Df\Payment\PlaceOrder' method='guest'/>
 * @used-by https://github.com/mage2pro/core/blob/2.12.5/Payment/etc/webapi.xml#L16
 * 		<service class='Df\Payment\PlaceOrder' method='registered'/>
 */
final class PlaceOrder {
	/**
	 * 2016-05-04
	 * 2017-04-04
	 * The arguments are arrived from Df_Checkout/js/action/place-order:
	 * https://github.com/mage2pro/core/blob/2.4.24/Checkout/view/frontend/web/js/action/place-order.js#L64-L66
	 * 2017-04-20
	 * 1) $qp в поле @see \Magento\Framework\DataObject::_data содержит код способа оплаты,
	 * а также ту дополнительную информацию, которую передала клиентская часть модуля оплаты.
	 * Например: [additional_data => [], method => "dfe_klarna"].
	 * 2) For a guest customer $cartId is a string like «63b25f081bfb8e4594725d8a58b012f7».
	 * 2023-01-28
	 * «Method's return type must be specified using @return annotation. See Df\Payment\PlaceOrder::guest()»
	 * on `bin/magento setup:upgrade`: https://github.com/mage2pro/core/issues/179
	 * 2026-02-02
	 * "`Magento\Framework\Reflection\TypeProcessor::getParamDocBlockTag()` incorrectly assumes
	 * that every parameter of a Web API exposed method has a `@param` comment in PHPDoc":
	 * https://github.com/mage2pro/core/issues/464
	 * @param string $cartId
	 * @param string $email
	 * @param IQP|QP $qp
	 * @param IQA|QA|null $ba
	 * 2017-04-04 Важно возвращать именно string: @see dfw_encode()
	 * @throws CouldNotSave|LE
	 * @return string
	 */
	function guest(string $cartId, string $email, IQP $qp, IQA $ba = null):string {return $this->p(
		true, $cartId, $email, $qp, $ba
	);}

	/**
	 * 2016-05-04
	 * 2023-01-28
	 * «Method's return type must be specified using @return annotation» on `bin/magento setup:upgrade`:
	 * https://github.com/mage2pro/core/issues/179
	 * 2026-02-02
	 * "`Magento\Framework\Reflection\TypeProcessor::getParamDocBlockTag()` incorrectly assumes
	 * that every parameter of a Web API exposed method has a `@param` comment in PHPDoc":
	 * https://github.com/mage2pro/core/issues/464
	 * @param string $cartId
	 * @param IQP|QP $qp
	 * @param IQA|QA|null $ba
	 * @throws CouldNotSave|LE
	 * @return string
	 */
	function registered(int $cartId, IQP $qp, IQA $ba = null):string {return $this->p(false, $cartId, $qp, $ba);}

	/**
	 * 2017-04-05
	 * The arguments are arrived from Df_Checkout/js/action/place-order:
	 * https://github.com/mage2pro/core/blob/2.4.24/Checkout/view/frontend/web/js/action/place-order.js#L64-L66
	 * 2017-04-20
	 * $qp в поле @see \Magento\Framework\DataObject::_data содержит код способа оплаты,
	 * а также ту дополнительную информацию, которую передала клиентская часть модуля оплаты.
	 * Например: [additional_data => [], method => "dfe_klarna"].
	 * @used-by self::guest()
	 * @used-by self::registered()
	 * @param int|string $cartId
	 * @param mixed ...$a
	 * 2017-04-04 Важно возвращать именно string: @see dfw_encode()
	 * @throws CouldNotSave|LE
	 */
	private function p(bool $isGuest, $cartId, ...$a):string {
		$saver = df_o($isGuest ? IGuest::class : IRegistered::class); /** @var IGuest|Guest|IRegistered|Registered $saver */
		$saver->savePaymentInformation($cartId, ...$a);
		return PlaceOrderInternal::p($cartId, $isGuest);
	}
}