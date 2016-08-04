<?php
namespace Df\Payment;
use Df\Config\Source\NoWhiteBlack as NWB;
use Df\Payment\R\Response;
use Df\Sales\Api\Data\TransactionInterface;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver as AssignObserver;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
abstract class Method implements MethodInterface {
	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's acceptPayment() used? https://mage2.pro/t/715
	 *
	 * @see \Magento\Payment\Model\MethodInterface::acceptPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L304-L312
	 * @see \Magento\Payment\Model\Method\AbstractMethod::acceptPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L696-L713
	 *
	 * @param II|I|OP $payment
	 * @return bool
	 */
	public function acceptPayment(II $payment) {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's assignData() used? https://mage2.pro/t/718
	 *
	 * @see \Magento\Payment\Model\MethodInterface::assignData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L304-L312
	 * @see \Magento\Payment\Model\Method\AbstractMethod::assignData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L762-L797
	 *
	 * ISSUES with @see \Magento\Payment\Model\Method\AbstractMethod::assignData():
	 * 1) The @see \Magento\Payment\Model\Method\AbstractMethod::assignData() method
	 * can be simplified: https://mage2.pro/t/719
	 * 2) The @see \Magento\Payment\Model\Method\AbstractMethod::assignData() method
	 * has a wrong PHPDoc declaration: https://mage2.pro/t/720
	 *
	 * @param DataObject $data
	 * @return $this
	 */
	public function assignData(DataObject $data) {
		/**
		 * 2016-05-03
		 * https://mage2.pro/t/718/3
		 * Раньше тут стояло:
		 * $this->ii()->addData($data->getData());
		 * Это имитировало аналогичный код метода
		 * @see \Magento\Payment\Model\Method\AbstractMethod::assignData()
		 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L772-L776
			if (is_array($data)) {
				$this->getInfoInstance()->addData($data);
			}
		 	elseif ($data instanceof \Magento\Framework\DataObject) {
				$this->getInfoInstance()->addData($data->getData());
			}
		 * Однако из новой версии метода
		 * @see \Magento\Payment\Model\Method\AbstractMethod::assignData()
		 * этот код пропал:
		 * https://github.com/magento/magento2/blob/ee6159/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L763-L792
		 * https://github.com/magento/magento2/commit/e4225bd7
		 *
		 * Раньше (до https://github.com/magento/magento2/commit/e4225bd7 )
		 * дополнительные данные приходили в $data->getData(),
		 * однако теперь они упакованы внутрь additional_data.
		 * @var array(string => mixed) $iia
		 */
		$iia = $data['additional_data'] ?: $data->getData();
		foreach ($this->iiaKeys() as $key) {
			/** @var string $key */
			$this->iiaSet($key, dfa($iia, $key));
		}
		$eventParams = [
			AssignObserver::METHOD_CODE => $this,
			/**
			 * 2016-05-29
			 * Константа @uses \Magento\Payment\Observer\AbstractDataAssignObserver::MODEL_CODE
			 * отсутствует в версиях ранее 2.1 RC1:
			 * https://github.com/magento/magento2/blob/2.1.0-rc1/app/code/Magento/Payment/Observer/AbstractDataAssignObserver.php#L25
			 * https://github.com/magento/magento2/blob/2.0.7/app/code/Magento/Payment/Observer/AbstractDataAssignObserver.php
			 *
			 * https://mail.google.com/mail/u/0/#inbox/154f9e0eb03982aa
			 */
			'payment_model' => $this->ii(),
			AssignObserver::DATA_CODE => $data
		];
		df_dispatch('payment_method_assign_data_' . $this->getCode(), $eventParams);
		df_dispatch('payment_method_assign_data', $eventParams);
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's authorize() used? https://mage2.pro/t/707
	 *
	 * @see \Magento\Payment\Model\MethodInterface::authorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L249-L257
	 * @see \Magento\Payment\Model\Method\AbstractMethod::authorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L603-L619
	 * @param II $payment
	 * @param float $amount
	 * @return $this
	 */
	public function authorize(II $payment, $amount) {return $this;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/t/644
	 * The method canAuthorize() should be removed from the interface
	 * @see \Magento\Payment\Model\MethodInterface,
	 * because it is used only by a particular interface's implementation
	 * @see \Magento\Payment\Model\Method\AbstractMethod
	 * and by vault payment methods.
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canAuthorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L63-L69
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canAuthorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L297-L306
	 * @return bool
	 */
	public function canAuthorize() {df_should_not_be_here(__METHOD__);}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/tags/capture
	 *
	 * Важно для витрины вернуть true, чтобы
	 * @see Df_Payment_Model_Action_Confirm::process() и другие аналогичные методы
	 * (например, @see Df_Alfabank_Model_Action_CustomerReturn::process())
	 * могли вызвать @see Mage_Sales_Model_Order_Invoice::capture().
	 *
	 * Для административной части возвращайте true только в том случае,
	 * если метод оплаты реально поддерживает операцию capture
	 * (т.е. имеет класс Df_XXX_Model_Request_Capture).
	 * Реализация этого класса позволит проводить двуступенчатую оплату:
	 * резервирование средств непосредственно в процессе оформления заказа
	 * и снятие средств посредством нажатия кнопки «Принять оплату» («Capture»)
	 * на административной странице счёта.
	 *
	 * Обратите внимание, что двуступенчатая оплата
	 * имеет смысл не только для дочернего данному класса @see Df_Payment_Model_Method_WithRedirect,
	 * но и для других прямых детей класса @see Df_Payment_Model_Method.
	 * @todo Например, правильным будет сделать оплату двуступенчатой для модуля «Квитанция Сбербанка»,
	 * потому что непосредственно по завершению заказа
	 * неправильно переводить счёт в состояние «Оплачен»
	 * (ведь он не оплачен! покупатель получил просто ссылку на квитанцию и далеко неочевидно,
	 * что он оплатит эту квитанцию).
	 * Вместо этого правильно будет оставлять счёт в открытом состоянии
	 * и переводить его в оплаченное состояние только после оплаты.
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L71-L77
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L308-L317
	 *
	 * USAGES
	 * How is payment method's canCapture() used?
	 * https://mage2.pro/t/645
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canCapture() used?
	 * https://mage2.pro/t/650
	 *
	 * @used-by \Magento\Payment\Model\Method\AbstractMethod::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L631-L638
	 *
	 * @used-by \Magento\Vault\Model\Method\Vault::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Vault/Model/Method/Vault.php#L222-L226
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment.php#L263-L267
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment::_invoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment.php#L532-L534
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment\Operations\AbstractOperation::invoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment/Operations/AbstractOperation.php#L69-L71
	 *
	 * @return bool
	 */
	public function canCapture() {return df_is_frontend();}

	/**
	 * 2016-02-10
	 * @override
	 * https://mage2.pro/tags/capture
	 *
	 * https://mage2.pro/t/658
	 * The @see \Magento\Payment\Model\MethodInterface::canCaptureOnce() is never used
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canCaptureOnce()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L87-L93
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canCaptureOnce()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L330-L339
	 *
	 * @return bool
	 */
	public function canCaptureOnce() {df_should_not_be_here(__METHOD__);}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/tags/capture
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canCapturePartial()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L79-L85
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canCapturePartial()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L325-L328
	 *
	 * USAGES
	 * How is payment method's canCapturePartial() used?
	 * https://mage2.pro/t/648
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canCapturePartial() used?
	 * https://mage2.pro/t/649
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment::canCapturePartial()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment.php#L302-L305
	 *
	 * @return bool
	 */
	public function canCapturePartial() {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's cancel() used? https://mage2.pro/t/710
	 *
	 * @see \Magento\Payment\Model\MethodInterface::cancel()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L279-L286
	 * @see \Magento\Payment\Model\Method\AbstractMethod::cancel()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L658-L669
	 * @param II $payment
	 * @return $this
	 */
	public function cancel(II $payment) {return $this;}

	/**
	 * 2016-02-10
	 * @override
	 * How is a payment method's canEdit() used? https://mage2.pro/t/672
	 * How is @see \Magento\Sales\Model\Order::canEdit() implemented and used? https://mage2.pro/t/673
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canEdit()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L133-L139
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canEdit()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L395-L404
	 * @return bool
	 */
	public function canEdit() {return true;}

	/**
	 * 2016-02-11
	 * @override
	 * https://mage2.pro/tags/payment-transaction
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canFetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L141-L147
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canFetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L406-L415
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/676
	 * How is a payment method's canFetchTransactionInfo() used?
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canFetchTransactionInfo() implemented and used?
	 * https://mage2.pro/t/677
	 */
	public function canFetchTransactionInfo() {return false;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/t/640
	 * The method canOrder() should be removed from the interface
	 * @see \Magento\Payment\Model\MethodInterface,
	 * because it is not used outside of a particular interface's implementation
	 * @see \Magento\Payment\Model\Method\AbstractMethod
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canOrder()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L55-L61
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canOrder()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L286-L295
	 * @return bool
	 */
	public function canOrder() {df_should_not_be_here(__METHOD__);}

	/**
	 * 2016-02-10
	 * @override
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат оплаты покупателю.
	 * https://mage2.pro/tags/refund
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canRefund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L95-L101
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canRefund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L341-L350
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/659
	 * How is a payment method's canRefund() used?
	 */
	public function canRefund() {return false;}

	/**
	 * 2016-02-10
	 * @override
	 * https://mage2.pro/tags/refund
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canRefundPartialPerInvoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L103-L109
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canRefundPartialPerInvoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L352-L361
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/663
	 * How is a payment method's canRefundPartialPerInvoice() used?
	 */
	public function canRefundPartialPerInvoice() {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's canReviewPayment() used? https://mage2.pro/t/714
	 *
	 * 2016-03-08
	 * http://stackoverflow.com/a/12814128
	 * «Magento's Order View block will check $order->canReviewPayment()
	 * which will look at the _canReviewPayment variable on the payment method,
	 * and if true, display two buttons on the Order View :
	 * "Accept Payment" and "Deny Payment".»
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canReviewPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L297-L302
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canReviewPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L688-L696
	 * @return bool
	 */
	public function canReviewPayment() {return false;}

	/**
	 * 2016-02-10
	 * @override
	 * The same as @see \Df\Payment\Method::canUseInternal(), but it is used for the frontend only.
	 * https://mage2.pro/t/671
	 * https://mage2.pro/tags/payment-can-use
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseCheckout()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L126-L131
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseCheckout()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L156-L161
	 * @return bool
	 */
	public function canUseCheckout() {return true;}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's canUseForCountry() used? https://mage2.pro/t/682
	 * The method @see \Magento\Payment\Model\Method\AbstractMethod::canUseForCountry()
	 * can be simplified: https://mage2.pro/t/683
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseForCountry()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L184-L190
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseForCountry()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L464-L482
	 * @param string $country
	 * @return bool
	 */
	public function canUseForCountry($country) {
		return NWB::is($this->s('country_restriction'), $country, df_csv_parse($this->s('countries')));
	}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's canUseForCurrency() used? https://mage2.pro/t/684
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseForCurrency()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L192-L199
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseForCurrency()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L484-L494
	 * @param string $currencyCode
	 * @return bool
	 */
	public function canUseForCurrency($currencyCode) {return true;}

	/**
	 * 2016-02-10
	 * @override
	 * Place in your custom canUseInternal() method a custom logic to decide
	 * whether the payment method need to be shown to a customer on the checkout screen.
	 * By default there is no custom login and the method just returns true.
	 * https://mage2.pro/t/670
	 * https://mage2.pro/tags/payment-can-use
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseInternal()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L118-L124
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseInternal()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L149-L154
	 * @return bool
	 */
	public function canUseInternal() {return true;}

	/**
	 * 2016-02-10
	 * @override
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированное разблокирование (возврат покупателю)
	 * ранее зарезервированных (но не снятых со счёта покупателя) средств
	 * https://mage2.pro/tags/void
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canVoid()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L111-L116
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canVoid()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L363-L372
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/666
	 * How is a payment method's canVoid() used?
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canVoid() implemented and used?
	 * https://mage2.pro/t/667
	 */
	public function canVoid() {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's capture() used? https://mage2.pro/t/708
	 *
	 * $amount содержит значение в учётной валюте системы.
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L37-L37
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L76-L82
	 *
	 * @see \Magento\Payment\Model\MethodInterface::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L259-L267
	 * @see \Magento\Payment\Model\Method\AbstractMethod::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L621-L638
	 * @param II $payment
	 * @param float $amount
	 * @return $this
	 */
	public function capture(II $payment, $amount) {return $this;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's denyPayment() used? https://mage2.pro/t/716
	 *
	 * @see \Magento\Payment\Model\MethodInterface::denyPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L314-L322
	 * @see \Magento\Payment\Model\Method\AbstractMethod::denyPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L715-L730
	 *
	 * @param II|I|OP $payment
	 * @return bool
	 */
	public function denyPayment(II $payment) {return false;}

	/**
	 * 2016-02-11
	 * @override
	 *
	 * @see \Magento\Payment\Model\MethodInterface::fetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L149-L158
	 * @see \Magento\Payment\Model\Method\AbstractMethod::fetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L417-L428
	 *
	 * @param II $payment
	 * @param string $transactionId
	 * @return array(string => mixed)
	 *
	 * USAGES
	 * https://mage2.pro/t/678
	 * How is a payment method's fetchTransactionInfo() used?
	 */
	public function fetchTransactionInfo(II $payment, $transactionId) {return [];}

	/**
	 * 2016-02-08
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L17-L23
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L496-L508
	 * @return string
	 */
	public function getCode() {return self::codeS();}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's getConfigData() used? https://mage2.pro/t/717
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getConfigData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L324-L332
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getConfigData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L742-L760
	 * @param string $field
	 * @param null|string|int|ScopeInterface $storeId [optional]
	 * @return string|null
	 */
	public function getConfigData($field, $storeId = null) {
		static $map = [
			/**
			 * 2016-02-16
			 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Config.php#L85-L105
			 * @uses \Df\Payment\Method::isActive()
			 */
			'active' => 'isActive'
			/**
			 * 2016-03-08
			 * @uses \Df\Payment\Method::cardTypes()
			 */
			,'cctypes' => 'cardTypes'
			/**
			 * 2016-03-15
			 * @uses \Df\Payment\Method::getConfigPaymentAction()
			 * Добавил, потому что в одном месте ядра 'payment_action' используется напрямую:
			 * https://github.com/magento/magento2/blob/8fd3e8/app/code/Magento/Sales/Model/Order/Payment.php#L339-L340
			 */
			,'payment_action' => 'getConfigPaymentAction'
			/**
			 * 2016-02-16
			 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Helper/Data.php#L265-L274
			 * @uses \Df\Payment\Method::getTitle()
			 */
			,'title' => 'getTitle'
		];
		return
			isset($map[$field])
			? call_user_func([$this, $map[$field]], $storeId)
			: $this->s($field, $storeId)
		;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's getConfigPaymentAction() used? https://mage2.pro/t/724
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getConfigPaymentAction()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L374-L381
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getConfigPaymentAction()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L854-L864
	 *
	 * 2016-05-07
	 * Сюда мы попадаем только из метода @used-by \Magento\Sales\Model\Order\Payment::place()
	 * причём там наш метод вызывается сразу из двух мест и по-разному.
	 *
	 * @return string
	 */
	public function getConfigPaymentAction() {return $this->s('payment_action');}

	/**
	 * 2016-02-08
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getFormBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L25-L32
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getFormBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L510-L518
	 *
	 * USAGE
	 * How is a payment method's getFormBlockType() used? https://mage2.pro/t/691
	 * @used-by \Magento\Payment\Helper\Data::getMethodFormBlock()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Helper/Data.php#L174
	 *
	 * 2016-02-29
	 * Этот метод используется только в административном интерфейсе
	 * (в сценарии создания и оплаты заказа администратором).
	 *
	 * @return string
	 */
	public function getFormBlockType() {
		df_assert(df_is_backend());
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_convention($this, 'Block\Form', \Df\Payment\Block\Form::class);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's getInfoBlockType() used? https://mage2.pro/t/687
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getInfoBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L25-L32
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getInfoBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L510-L518
	 *
	 * @return string
	 */
	public function getInfoBlockType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_convention($this, 'Block\Info',
				$this->useConfigurableBlockInfo()
					? \Df\Payment\Block\ConfigurableInfo::class
					: \Df\Payment\Block\Info::class
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's getInfoInstance() used? https://mage2.pro/t/696
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L210-L218
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L531-L545
	 * @throws LE
	 * @return II|I|OP|QP
	 */
	public function getInfoInstance() {
		if (!$this->_infoInstance) {
			throw new LE(__('We cannot retrieve the payment information object instance.'));
		}
		return $this->_infoInstance;
	}

	/**
	 * 2016-02-09
	 * @override
	 * How is a payment method's getStore() used? https://mage2.pro/t/695
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L49-L53
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L278-L284
	 * @return int
	 */
	public function getStore() {return $this->_storeId;}

	/**
	 * 2016-02-08
	 * @override
	 * How is a payment method's getTitle() used? https://mage2.pro/t/692
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getTitle()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L34-L40
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getTitle()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L732-L740
	 * @return string
	 */
	public function getTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->s('title' . (df_is_backend() ? '_backend' : ''), null, function() {
					return df_class_second($this);
				})
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's initialize() used? https://mage2.pro/t/722
	 *
	 * @see \Magento\Payment\Model\MethodInterface::initialize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L361-L372
	 * @see \Magento\Payment\Model\Method\AbstractMethod::initialize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L838-L852
	 *
	 * @param string $paymentAction
	 * @param object $stateObject
	 * @return $this
	 */
	public function initialize($paymentAction, $stateObject) {return $this;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/t/641
	 * The method isActive() should be removed from the interface
	 * @see \Magento\Payment\Model\MethodInterface,
	 * because it is not used outside of a particular interface's implementation
	 * @see \Magento\Payment\Model\Method\AbstractMethod
	 * and by vault payment methods.
	 *
	 * Но раз уж этот метод присутствует в интерфейсе,
	 * то я его использую в методе @used-by \Df\Payment\Method::s()
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isActive()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L352-L359
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isActive()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L827-L836
	 *
	 * https://mage2.pro/t/634
	 * https://mage2.pro/t/635
	 * «The @see \Magento\Payment\Model\Method\AbstractMethod::isActive() method
	 * has a wrong PHPDoc type for the $storeId parameter»
	 * «The @see  \Magento\Payment\Model\MethodInterface::isActive() method
	 * has a wrong PHPDoc type for the $storeId parameter»
	 *
	 * @param null|string|int|ScopeInterface $storeId [optional]
	 * @return bool
	 */
	public function isActive($storeId = null) {
		return $this->s()->b('enable', $storeId);
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's isAvailable() used? https://mage2.pro/t/721
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L343-L350
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L805-L825
	 *
	 * @param CartInterface $quote [optional]
	 * @return bool
	 */
	public function isAvailable(CartInterface $quote = null) {
		/** @var bool $result */
		$result =
			($this->availableInBackend() || !df_is_backend())
			&& $this->isActive($quote ? $quote->getStoreId() : null)
		;
		if ($result) {
			/** @var DataObject $checkResult */
			$checkResult = new DataObject(['is_available' => true]);
			df_dispatch('payment_method_is_active', [
				'result' => $checkResult, 'method_instance' => $this, 'quote' => $quote
			]);
			$result = $checkResult['is_available'];
		}
		return $result;
	}

	/**
	 * 2016-02-11
	 * @override
	 * Насколько я понял, isGateway должно возвращать true,
	 * если процесс оплаты должен происходить непосредственно на странице оформления заказа,
	 * без перенаправления на страницу платёжной системы.
	 * В Российской сборке Magento так пока работает только метод @see Df_Chronopay_Model_Gate,
	 * однако он изготовлен давно и по устаревшей технологии,
	 * и поэтому не является наследником класса @see Df_Payment_Model_Method
	 *
	 * How is a payment method's isGateway() used? https://mage2.pro/t/679
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isGateway()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L160-L166
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isGateway()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L431-L440
	 * @return bool
	 */
	public function isGateway() {return false;}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's isInitializeNeeded() used? https://mage2.pro/t/681
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isInitializeNeeded()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L176-L182
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isInitializeNeeded()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L454-L462
	 * @return bool
	 */
	public function isInitializeNeeded() {return false;}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's isOffline() used? https://mage2.pro/t/680
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isOffline()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L168-L174
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isOffline()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L442-L451
	 * @return bool
	 */
	public function isOffline() {return false;}

	/**
	 * 2016-02-14
	 * @override
	 * How is a payment method's order() used? https://mage2.pro/t/701
	 *
	 * @see \Magento\Payment\Model\MethodInterface::order()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L239-L247
	 * @see \Magento\Payment\Model\Method\AbstractMethod::order()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L585-L601
	 * @param II $payment
	 * @param float $amount
	 * @return $this
	 */
	public function order(II $payment, $amount) {
		df_should_not_be_here(__METHOD__);
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's refund() used? https://mage2.pro/t/709
	 *
	 * @see \Magento\Payment\Model\MethodInterface::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L269-L277
	 * @see \Magento\Payment\Model\Method\AbstractMethod::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L640-L656
	 * @param II|I|OP $payment
	 * @param float $amount
	 * @return $this
	 */
	public function refund(II $payment, $amount) {return $this;}

	/**
	 * 2016-07-18
	 * @return Response|null
	 */
	public function responseF() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(df_first($this->responses()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-07-18
	 * @return Response|null
	 */
	public function responseL() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(df_last($this->responses()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-07-18
	 * @return Response[]
	 */
	public function responses() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $class */
			$class = df_convention($this, 'Response');
			$this->{__METHOD__} = array_map(function(T $t) use($class) {
				return call_user_func([$class, 'i'], df_trans_raw_details($t));
			}, $this->transChildren());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-18
	 * @return Response[]
	 */
	public function responsesSucc() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_filter($this->responses(), function(Response  $r) {
				return $r->validAndSuccessful();
			});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-13
	 * @used-by \Df\Payment\PlaceOrderInternal::ss()
	 * @param string $key [optional]
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @param mixed|callable $default [optional]
	 * @return Settings
	 */
	public function s($key = '', $scope = null, $default = null) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Settings::s(df_convention($this, 'Settings'));
		}
		return
			df_null_or_empty_string($key)
			? $this->{__METHOD__}
			: $this->{__METHOD__}->v($key, $scope, $default)
		;
	}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's setInfoInstance() used? https://mage2.pro/t/697
	 *
	 * @see \Magento\Payment\Model\MethodInterface::setInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L220-L228
	 * @see \Magento\Payment\Model\Method\AbstractMethod::setInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L547-L557
	 * @param II|I|OP|QP $info
	 * @return void
	 */
	public function setInfoInstance(II $info) {$this->_infoInstance = $info;}

	/**
	 * 2016-02-09
	 * @override
	 * How is a payment method's setStore() used? https://mage2.pro/t/693
	 *
	 * @see \Magento\Payment\Model\MethodInterface::setStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L42-L47
	 * @see \Magento\Payment\Model\Method\AbstractMethod::setStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L270-L276
	 * @param int $storeId
	 * @return void
	 */
	public function setStore($storeId) {
		$this->_storeId = (int)$storeId;
		$this->s()->setScope($storeId);
	}

	/**
	 * 2016-07-28
	 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
	 * @return string
	 */
	public function titleDetailed() {return $this->getTitle();}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's validate() used? https://mage2.pro/t/698
	 *
	 * @see \Magento\Payment\Model\MethodInterface::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L230-L237
	 * @see \Magento\Payment\Model\Method\AbstractMethod::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L566-L583
	 * @throws LE
	 * @return $this
	 */
	public function validate() {
		if (!$this->canUseForCountry($this->infoOrderOrQuote()->getBillingAddress()->getCountryId())) {
			throw new LE(__(
				'You can\'t use the payment type you selected to make payments to the billing country.'
			));
		}
		$this->remindTestMode();
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's void() used? https://mage2.pro/t/712
	 *
	 * @see \Magento\Payment\Model\MethodInterface::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L288-L295
	 * @see \Magento\Payment\Model\Method\AbstractMethod::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L671-L686
	 * @param II|I|OP $payment
	 * @return $this
	 */
	public function void(II $payment) {return $this;}

	/**
	 * 2016-07-10
	 * @param string $id
	 * @param array(string => mixed) $data
	 */
	protected function addTransaction($id, array $data) {
		$this->ii()->setTransactionId(self::transactionIdL2G($id));
		$this->iiaSetTR($data);
		//$this->ii()->setIsTransactionClosed(false);
		/**
		 * 2016-07-10
		 * @uses TransactionInterface::TYPE_PAYMENT — это единственный транзакции
		 * без специального назначения, и поэтому мы можем безопасно его использовать.
		 */
		$this->ii()->addTransaction(TransactionInterface::TYPE_PAYMENT);
	}

	/**
	 * 2016-02-29
	 * Решил, что значением пол умолчанию разумно сделать false.
	 * @used-by \Df\Payment\Method::isAvailable()
	 * @return bool
	 */
	protected function availableInBackend() {return false;}

	/**
	 * 2016-03-08
	 * @used-by \Df\Payment\Method::getConfigData()
	 * @return string
	 */
	protected function cardTypes() {return $this->s('cctypes');}

	/**
	 * 2016-03-06
	 * @param string|null $key [optional]
	 * @return II|I|OP|QP|mixed
	 */
	protected function ii($key = null) {
		/** @var II|I|OP|QP $result */
		$result = $this->getInfoInstance();
		return is_null($key) ? $result : $result[$key];
	}

	/**
	 * 2016-03-06
	 * @param ...$keys
	 * @return mixed|array(string => mixed)
	 */
	protected function iia(...$keys) {
		return !$keys ? $this->ii()->getAdditionalInformation() : (
			1 === count($keys)
			? $this->ii()->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($this->ii()->getAdditionalInformation(), $keys)
		);
	}

	/**
	 * 2016-05-03
	 * @used-by \Df\Payment\Method::assignData()
	 * @return string[]
	 */
	protected function iiaKeys() {return [];}

	/**
	 * 2016-07-10
	 * @param array(string => mixed) $values
	 * @return void
	 * @throws LE
	 */
	protected function iiaAdd(array $values) {
		foreach ($values as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$this->ii()->setAdditionalInformation($key, $value);
		}
	}

	/**
	 * 2016-07-10
	 * @param array(string => mixed) $values
	 * @return void
	 * @throws LE
	 */
	protected function iiaAddT(array $values) {
		foreach ($values as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$this->ii()->setTransactionAdditionalInfo($key, $value);
		}
	}

	/**
	 * 2016-03-06
	 * @param string|array(string => mixed) $key [optional]
	 * @param mixed|null $value [optional]
	 * @return void
	 * @throws LE
	 */
	protected function iiaSet($key, $value = null) {
		$this->ii()->setAdditionalInformation($key, $value);
	}

	/**
	 * 2016-03-15
	 * @return bool
	 */
	protected function isTheCustomerNew() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_customer_is_new($this->o()->getCustomerId());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-03-15
	 * @return O
	 */
	protected function o() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_order_by_payment($this->ii());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-03-15
	 * @return int|null
	 */
	protected function oi() {return $this->o()->getId();}

	/**
	 * 2016-07-10
	 * @param string $id
	 * @param string $uri
	 * @param array(string => mixed) $data
	 */
	protected function saveRequest($id, $uri, array $data) {
		$this->addTransaction($id, [self::TRANSACTION_PARAM__URL => $uri] + $data);
	}

	/**
	 * 2016-05-06
	 * https://mage2.pro/t/898/3
	 * Использовать ли @see \Df\Payment\Block\ConfigurableInfo вместо @see \Df\Payment\Block\Info
	 * @used-by \Df\Payment\Method::getInfoBlockType()
	 * @return bool
	 */
	protected function useConfigurableBlockInfo() {return true;}

	/**
	 * 2016-07-10
	 * @param array(string => mixed) $values
	 * @return void
	 */
	private function iiaSetTR(array $values) {df_payment_set_transaction_info($this->ii(), $values);}

	/**
	 * 2016-02-12
	 * @return O|Q
	 */
	private function infoOrderOrQuote() {
		/** @var II|I|OP|QP $info */
		$info = $this->ii();
		return $info instanceof OP ? $info->getOrder() : $info->getQuote();
	}

	/**
	 * 2016-07-13
	 * @used-by \Df\Payment\Method::validate()
	 * @return void
	 */
	private function remindTestMode() {
		if ($this->s()->test()) {
			$this->iiaSet(self::II__TEST, true);
		}
	}

	/**
	 * 2016-07-13
	 * @return T[]
	 */
	private function transChildren() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->transParent()
				? []
				: df_usort($this->transParent()->getChildTransactions(),
					function(T $a, T $b) {return $a->getId() - $b->getId();}
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-13
	 * 2016-07-28
	 * Транзакции может не быть в случае каких-то сбоев.
	 * Решил не падать из-за этого, потому что мы можем попасть сюда
	 * в невинном сценарии отображения таблицы заказов
	 * (в контексте рисования колонки с названиями способов оплаты).
	 * @return T|null
	 */
	private function transParent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(df_trans_by_payment_first($this->ii()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-02-12
	 * @used-by \Df\Payment\Method::getInfoInstance()
	 * @used-by \Df\Payment\Method::setInfoInstance()
	 * @var II|I|OP|QP
	 */
	private $_infoInstance;

	/**
	 * 2016-02-09
	 * @used-by \Df\Payment\Method::getStore()
	 * @used-by \Df\Payment\Method::setStore()
	 * @var int
	 */
	private $_storeId;

	/**
	 * 2016-05-11
	 * This flag is to be used in webhook scenarios.
	 * The ID comes from the payment gateway
	 * We need to store it, as Magento doesn't create automatic type IDs.
	 * <parent id>-capture
	 * @used-by \Dfe\CheckoutCom\Method::capture()
	 * @used-by \Dfe\CheckoutCom\Method::refund()
	 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
	 */
	const CUSTOM_TRANS_ID = 'df_transaction_id';

	/**
	 * 2016-07-13
	 * @used-by \Df\Payment\Method::remindTestMode()
	 */
	const II__TEST = 'df_test';

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\Method::saveRequest()
	 * @used-by \Df\Payment\R\Response::requestParams()
	 * @used-by \Df\Payment\R\Response::requestUrl()
	 */
	const TRANSACTION_PARAM__URL = '_URL';

	/**
	 * 2016-07-10
	 * @param string $globalId
	 * @return string
	 */
	public static function transactionIdG2L($globalId) {
		return df_trim_text_left($globalId, self::transactionIdPrefix());
	}

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\Method::addTransaction()
	 * @used-by \Df\Payment\R\Response::idL2G()
	 * @param string $localId
	 * @return string
	 */
	public static function transactionIdL2G($localId) {
		return self::transactionIdPrefix() . $localId;
	}

	/**
	 * 2016-07-10
	 * 2016-02-16
	 * @see \Dfe\Stripe\Method => «dfe_stripe»
	 * @see \Dfe\CheckoutCom\Method => «dfe_checkout_com»
	 * @return string
	 */
	private static function codeS() {
		/** @var array(string => string) $cache */
		static $cache;
		if (!isset($cache[static::class])) {
			$cache[static::class] =
				df_cts_lc_camel(str_replace('\\Method', '', df_cts(static::class)), '_')
			;
		}
		return $cache[static::class];
	}

	/**
	 * 2016-07-10
	 * @return string
	 */
	private static function transactionIdPrefix() {return self::codeS() . '-';}
}