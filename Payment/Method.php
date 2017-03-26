<?php
namespace Df\Payment;
use Df\Config\Source\NoWhiteBlack as NWB;
use Df\Core\Exception as DFE;
use Df\Payment\Init\Action as InitAction;
use Magento\Framework\App\Area;
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
use Magento\Store\Model\Store;
/**
 * 2016-02-08
 * @see \Df\GingerPaymentsBase\Method
 * @see \Df\PaypalClone\Method
 * @see \Df\StripeClone\Method
 * @see \Dfe\CheckoutCom\Method
 * @see \Dfe\Klarna\Method
 * @see \Dfe\Square\Method
 * @see \Dfe\TwoCheckout\Method
 */
abstract class Method implements MethodInterface {
	/**
	 * 2016-11-15
	 * 2017-02-08
	 * Замечание №1
	 * The result should be in the basic monetary unit (like dollars), not in fractions (like cents).
	 * Замечание №2
	 * Я пришёл к выводу, что у КАЖДОГО платёжного сервиса имеются ограничения на приём платежей.
	 * Поэтому пусть КАЖДЫЙ платёжный модуль явно декларирует эти ограничения.
	 * Допустимы следующие форматы результата:
	 * 1) null или [] — отсутствие лимитов.
	 * 2) [min, max] — общие лимиты для всех валют
	 * 3) callable — лимиты вычисляются динамически для конкретной валюты
	 * 4) ['USD' => [min, max], '*' => [min, max]] — лимиты заданы с таблицей,
	 * причём '*' — это лимиты по умолчанию.
	 * В случаях №2 и №4 min и/или max может быть равно null: это означает отсутствие лимита.
	 * @used-by isAvailable()
	 * @see \Df\GingerPaymentsBase\Method::amountLimits()
	 * @see \Dfe\AllPay\Method::amountLimits()
	 * @see \Dfe\CheckoutCom\Method::amountLimits()
	 * @see \Dfe\Iyzico\Method::amountLimits()
	 * @see \Dfe\Klarna\Method::amountLimits()
	 * @see \Dfe\Omise\Method::amountLimits()
	 * @see \Dfe\Paymill\Method::amountLimits()
	 * @see \Dfe\SecurePay\Method::amountLimits()
	 * @see \Dfe\Square\Method::amountLimits()
	 * @see \Dfe\Spryng\Method::amountLimits()
	 * @see \Dfe\Stripe\Method::amountLimits()
	 * @see \Dfe\TwoCheckout\Method::amountLimits()
	 * @return null|[]|\Closure|array(int|float|null)|array(string => array(int|float|null))
	 */
	abstract protected function amountLimits();

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
	 * 2016-05-09
	 * A «Flagged» payment can be handled the same way as an «Authorised» payment:
	 * we can «capture» or «void» it.
	 *
	 * @param II|I|OP $payment
	 * @return bool
	 */
	final function acceptPayment(II $payment) {
		// 2016-03-15
		// The obvious $this->charge($payment) is not quite correct,
		// because no invoice will be created in this case.
		$payment->capture();
		return true;
	}

	/**
	 * 2016-08-14
	 * 2017-01-10
	 * Этот метод служит единой точкой входа для всех платёжных транзакций нашего класса.
	 * Сведение их в единую точку позволяет нам централизованно:
	 * 1) Отфлильтровывать случаи выполнения транзакций из webhooks
	 * (в этом случае мы не обращаемся к API платёжной системы,
	 * потому что на стороне платёжной системы транзакция уже проведена,
	 * о чём мы и получили оповещение в webhook).
	 * 2) Обрабатывать исключительные ситуации.
	 * При этом каждый платёжный модуль может иметь свои индивидуальные особенности
	 * обработки исключительных ситуаций, а здесь мы лишь выполняем общую, универсальную
	 * часть такой обработки.
	 * 3) Инициализировать библиотеку платёжной системы.
	 * @used-by authorize()
	 * @used-by capture()
	 * @used-by refund()
	 * @used-by void()
	 * @used-by \Df\Payment\Init\Action::action()
	 * @param string|\Closure $f
	 * @param mixed[] ...$args
	 * @return mixed
	 */
	final function action($f, ...$args) {
		/** @var mixed $result */
		$result = null;
		if (!$this->ii(self::WEBHOOK_CASE)) {
			dfp_sentry_tags($this);
			/** @var string $actionS */
			df_sentry_tags($this, ['Payment Action' => $actionS = df_caller_f()]);
			try {
				$this->s()->init();
				// 2017-01-10
				// Такой код корректен, проверял: https://3v4l.org/Efj63
				$result = call_user_func($f instanceof \Closure ? $f : [$this, $f], ...$args);
				/**
				 * 2017-01-31
				 * В настоящее время опция «Log the API requests and responses?»
				 * присутствует у модулей allPay и SecurePay:
				 * 1) allPay: https://github.com/mage2pro/allpay/blob/1.1.25/etc/adminhtml/system.xml?ts=4#L413-L426
				 * 2) SecurePay: https://github.com/mage2pro/securepay/blob/1.1.17/etc/adminhtml/system.xml?ts=4#L156-L169
				 * У остальных моих платёжных модулей этой опции пока нет,
				 * там функциональность логирования пока включена намертво.
				 *
				 * 2017-02-01
				 * До сегодняшнего дня Stripe-подобные модули для каждой платёжной операции
				 * создавали как минимум (не считая webhooks) 3 записи в логах:
				 * 1) Stripe: getConfigPaymentAction
				 * 2) [Stripe] chargeNew
				 * 3) Stripe: capture
				 * №1 и №3 создавались как раз отсюда, из action()
				 * Нам не нужно так много записей для единственной операции,
				 * поэтому добавил сейчас возможность отключать логирование в action().
				 */
				if ($this->needLogActions() && $this->s()->log()) {
					df_sentry($this, "{$this->titleB()}: $actionS");
				}
			}
			catch (\Exception $e) {
				// 2017-01-10
				// Конвертация исключительных ситуаций библиотеки платёжной системы в наши.
				// Исключительные ситуации библиотеки платёжной системы имеют свою внутреннуюю структуру,
				// да и их диагностические сообщения — это не всегда то, что нам нужно.
				// По этой причине мы их конвертируем в свои.
				// Пока данная функциональность используется модулем Stripe.
				df_log($e = $this->convertException($e));
				/**
				 * 2016-03-17
				 * Чтобы система показала наше сообщение вместо общей фразы типа
				 * «We can't void the payment right now», надо вернуть объект именно класса
				 * @uses \Magento\Framework\Exception\LocalizedException
				 * https://mage2.pro/t/945
				 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Controller/Adminhtml/Order/VoidPayment.php#L20-L30
				 */
				throw df_le($e);
			}
		}
		return $result;
	}

	/**
	 * 2016-09-07
	 * Конвертирует денежную величину (в валюте платежа) из обычного числа в формат платёжной системы.
	 * В частности, некоторые платёжные системы хотят денежные величины в копейках (Checkout.com),
	 * обязательно целыми (allPay) и т.п.
	 *
	 * 2016-09-08
	 * Обратная операция по отношению к @see amountParse()
	 *
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\Operation::amountFormat()
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @used-by \Df\StripeClone\Method::charge()
	 * @see \Dfe\TwoCheckout\Method::amountFormat()
	 * @param float $amount
	 * @return float|int|string
	 */
	function amountFormat($amount) {return round($amount * $this->amountFactor());}

	/**
	 * 2016-09-08
	 * Конвертирует денежную величину из формата платёжной системы в обычное число.
	 * Обратная операция по отношению к @see amountFormat()
	 *
	 * @used-by dfp_refund()
	 * @used-by \Dfe\Stripe\Method::amountLimits()
	 * @param float|int|string $amount
	 * @return float
	 */
	final function amountParse($amount) {return $amount / $this->amountFactor();}

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
	final function assignData(DataObject $data) {
		/**
		 * 2016-05-03
		 * https://mage2.pro/t/718/3
		 * Раньше тут стояло:
		 * $this->ii()->addData($data->getData());
		 * Это имитировало аналогичный код метода
		 * @see \Magento\Payment\Model\Method\AbstractMethod::assignData()
		 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L772-L776
		 *	if (is_array($data)) {
		 *		$this->getInfoInstance()->addData($data);
		 *	}
		 * 	elseif ($data instanceof \Magento\Framework\DataObject) {
		 *		$this->getInfoInstance()->addData($data->getData());
		 * 	}
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
			/** @var string|null $value */
			$value = dfa($iia, $key);
			if (!is_null($value)) {
				$this->iiaSet($key, $value);
			}
		}
		/** @var array(string => mixed) $eventParams */
		df_dispatch("payment_method_assign_data_{$this->getCode()}", $eventParams = [
			AssignObserver::METHOD_CODE => $this,
			/**
			 * 2016-05-29
			 * Константа @uses \Magento\Payment\Observer\AbstractDataAssignObserver::MODEL_CODE
			 * отсутствует в версиях ранее 2.1 RC1:
			 * https://github.com/magento/magento2/blob/2.1.0-rc1/app/code/Magento/Payment/Observer/AbstractDataAssignObserver.php#L25
			 * https://github.com/magento/magento2/blob/2.0.7/app/code/Magento/Payment/Observer/AbstractDataAssignObserver.php
			 * https://mail.google.com/mail/u/0/#inbox/154f9e0eb03982aa
			 */
			'payment_model' => $this->ii(),
			AssignObserver::DATA_CODE => $data
		]);
		df_dispatch('payment_method_assign_data', $eventParams);
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's authorize() used? https://mage2.pro/t/707
	 * @see \Magento\Payment\Model\MethodInterface::authorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L249-L257
	 * @see \Magento\Payment\Model\Method\AbstractMethod::authorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L603-L619
	 * @param II $payment
	 * @param float $amount
	 * @return $this
	 */
	final function authorize(II $payment, $amount) {return $this->action(
		function() use($payment, $amount) {
			/**
			 * 2016-09-05
			 * Отныне валюта платёжных транзакций настраивается администратором опцией
			 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
			 * @see \Df\Payment\Settings::currency()
			 *
			 * 2016-08-19
			 * Со вчерашнего для мои платёжные модули выполняют платёжные транзакции
			 * не в учётной валюте системы, а в валюте заказа (т.е., витринной валюте).
			 *
			 * Однако это привело к тому, что операция авторизации
			 * стала помечать заказы (платежи) как «Suspected Fraud» (STATUS_FRAUD).
			 * Это происходит из-за кода метода
			 * @see \Magento\Sales\Model\Order\Payment\Operations\AuthorizeOperation::authorize()
			 *		$isSameCurrency = $payment->isSameCurrency();
			 *		if (!$isSameCurrency || !$payment->isCaptureFinal($amount)) {
			 *			$payment->setIsFraudDetected(true);
			 *		}
			 *
			 * Метод @see \Magento\Sales\Model\Order\Payment::isSameCurrency() работает так:
			 *		return
			 *			!$this->getCurrencyCode()
			 *			|| $this->getCurrencyCode() == $this->getOrder()->getBaseCurrencyCode()
			 *		;
			 * По умолчанию $this->getCurrencyCode() возвращает null,
			 * и поэтому isSameCurrency() возвращает true.
			 * Magento, получается, думает, что платёж выполняется в учёной валюте системы,
			 * но вызов $payment->isCaptureFinal($amount) вернёт false,
			 * потому что $amount — размер платежа в учётной валюте системы, а метод устроен так:
			 * @see \Magento\Sales\Model\Order\Payment::isCaptureFinal()
			 *	$total = $this->getOrder()->getTotalDue();
			 *	return
			 *			$this->amountFormat($total, true)
			 *		==
			 *			$this->amountFormat($amountToCapture, true)
			 *	;
			 * Т.е. метод сравнивает размер подлежащей оплате стоимости заказа в валюте заказа
			 * с размером текущего платежа, который в учётной валюте системы,
			 * и поэтому вот метод возвращает false.
			 *
			 * Самым разумным решением этой проблемы мне показалось
			 * ручное убирание флага IsFraudDetected
			 */
			if ($payment instanceof OP) {
				$payment->setIsFraudDetected(false);
			}
			$this->charge($this->cFromBase($amount), $capture = false);
			return $this;
		}
	);}

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
	 * @return void
	 */
	final function canAuthorize() {df_should_not_be_here();}

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
	 * 2016-09-30
	 * Сегодня заметил, что метод @uses \Magento\Framework\App\State::getAreaCode()
	 * стал возвращать значение @see \Magento\Framework\App\Area::AREA_WEBAPI_REST
	 * при выполнении платежа на витрине.
	 *
	 * 2016-09-30
	 * Используемые константы присутствуют уже в релизе 2.0.0, потому использовать их безопасно:
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Area.php
	 *
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::canCapture()
	 * @see \Dfe\CheckoutCom\Method::canCapture()
	 * @see \Dfe\TwoCheckout\Method::canCapture()
	 *
	 * @return bool
	 */
	function canCapture() {return df_area_code_is(Area::AREA_FRONTEND, Area::AREA_WEBAPI_REST);}

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
	 * @return void
	 */
	final function canCaptureOnce() {df_should_not_be_here();}

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
	 * 2017-02-08
	 * @see \Dfe\CheckoutCom\Method::canCapturePartial()
	 * @see \Dfe\Omise\Method::canCapturePartial()
	 * @see \Dfe\Spryng\Method::canCapturePartial()
	 * @see \Dfe\Stripe\Method::canCapturePartial()
	 *
	 * @return bool
	 */
	function canCapturePartial() {return false;}

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
	final function cancel(II $payment) {return $this;}

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
	final function canEdit() {return true;}

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
	final function canFetchTransactionInfo() {return false;}

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
	 * @return void
	 */
	final function canOrder() {df_should_not_be_here();}

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
	 *
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::canRefund()
	 * @see \Dfe\CheckoutCom\Method::canRefund()
	 * @see \Dfe\SecurePay\Method::canRefund()
	 * @see \Dfe\TwoCheckout\Method::canRefund()
	 */
	function canRefund() {return false;}

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
	 *
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::canRefundPartialPerInvoice()
	 * @see \Dfe\CheckoutCom\Method::canRefundPartialPerInvoice()
	 * @see \Dfe\TwoCheckout\Method::canRefundPartialPerInvoice()
	 */
	function canRefundPartialPerInvoice() {return false;}

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
	 *
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::canReviewPayment()
	 * @see \Dfe\CheckoutCom\Method::canReviewPayment()
	 */
	function canReviewPayment() {return false;}

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
	final function canUseCheckout() {return true;}

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
	final function canUseForCountry($country) {return NWB::is(
		$this->s('country_restriction'), $country, df_csv_parse($this->s('countries'))
	);}

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
	final function canUseForCurrency($currencyCode) {return true;}

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
	final function canUseInternal() {return true;}

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
	 *
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::canVoid()
	 * @see \Dfe\CheckoutCom\Method::canVoid()
	 */
	function canVoid() {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's capture() used? https://mage2.pro/t/708
	 *
	 * Используется только отсюда:
	 * @used-by \Magento\Sales\Model\Order\Payment\Operations\CaptureOperation::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L76-L82
	 * Параметр $payment можно игнорировать, потому что он уже доступен в виде свойства объекта.
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
	 * В спецификации PHPDoc интерфейса указано, что метод должен возвращать $this,
	 * но реально возвращаемое значение ядром не используется,
	 * поэтому спокойно не возвращаю ничего.
	 *
	 * @uses charge()
	 */
	final function capture(II $payment, $amount) {
		$this->action('charge', $this->cFromBase($amount));
		return $this;
	}

	/**
	 * 2016-08-20
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * 2017-02-08
	 * Конвертирует $amount из учётной валюты в валюту платежа.
	 * @see \Df\Payment\Settings::currency()
	 * @used-by \Df\Payment\Init\Action::amount()
	 * @used-by \Df\Payment\Method::authorize()
	 * @used-by \Df\Payment\Method::capture()
	 * @used-by \Df\Payment\Method::refund()
	 * @used-by \Df\Payment\Operation::cFromBase()
	 * @param float $amount
	 * @return float
	 * @uses \Df\Payment\Settings::cFromBase()
	 */
	final function cFromBase($amount) {return $this->convert($amount);}

	/**
	 * 2016-09-06
	 * 2017-02-08
	 * Конвертирует $amount из валюты заказа в валюту платежа.
	 * @used-by \Df\Payment\Operation::cFromOrder()
	 * @used-by \Dfe\TwoCheckout\LineItem\Product::priceRaw()
	 * @param float $amount
	 * @return float
	 */
	final function cFromOrder($amount) {return $this->convert($amount);}

	/**
	 * 2016-09-08
	 * 2017-02-08
	 * Конвертирует $amount из валюты платежа в учётную.
	 * @param float $amount
	 * @return float
	 */
	final function cToBase($amount) {return $this->convert($amount);}

	/**
	 * 2016-09-08
	 * 2017-02-08
	 * Конвертирует $amount из валюты платежа в валюту заказа.
	 * @param float $amount
	 * @return float
	 */
	final function cToOrder($amount) {return $this->convert($amount);}

	/**
	 * 2016-09-07
	 * Код платёжной валюты.
	 * @used-by amountFormat()
	 * @used-by \Df\Payment\Operation::currencyC()
	 * @used-by \Dfe\Stripe\Method::minimumAmount()
	 * @return string
	 */
	final function cPayment() {return dfc($this, function() {return $this->s()->currencyC($this->oq());});}

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
	 *
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::denyPayment()
	 * @see \Dfe\CheckoutCom\Method::denyPayment()
	 */
	function denyPayment(II $payment) {return false;}

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
	final function fetchTransactionInfo(II $payment, $transactionId) {return [];}

	/**
	 * 2016-02-08
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L17-L23
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L496-L508
	 * @return string
	 */
	final function getCode() {return self::codeS();}

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
	final function getConfigData($field, $storeId = null) {
		static $map = [
			/**
			 * 2016-02-16
			 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Config.php#L85-L105
			 * @uses \Df\Payment\Method::isActive()
			 */
			'active' => 'isActive'
			/** 2016-03-08 @uses \Df\Payment\Method::cardTypes() */
			,'cctypes' => 'cardTypes'
			/**
			 * 2016-03-15
			 * @uses \Df\Payment\Method::getConfigPaymentAction()
			 * Добавил, потому что в одном месте ядра 'payment_action' используется напрямую:
			 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L339-L340
			 */
			,'payment_action' => 'getConfigPaymentAction'
			/**
			 * 2016-02-16
			 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Helper/Data.php#L265-L274
			 * @uses \Df\Payment\Method::getTitle()
			 */
			,'title' => 'getTitle'
		];
		return isset($map[$field]) ? call_user_func([$this, $map[$field]], $storeId) :
			$this->s($field, $storeId)
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
	 * 2016-12-24
	 * @used-by \Magento\Sales\Model\Order\Payment::place()
	 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment.php#L334-L355
	 *
	 * 2017-02-08
	 * @see \Dfe\CheckoutCom\Method::getConfigPaymentAction()
	 * @return string|null
	 */
	function getConfigPaymentAction() {return InitAction::p($this);}

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
	 * @return void
	 */
	final function getFormBlockType() {df_assert(df_is_backend()); df_should_not_be_here();}

	/**
	 * 2016-02-11
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getInfoBlockType()
	 * How is a payment method's getInfoBlockType() used? https://mage2.pro/t/687
	 * 
	 * 2016-08-29
	 * Метод вызывается единократно, поэтому кэшировать результат не надо:
	 * @used-by \Magento\Payment\Helper\Data::getInfoBlock()
	 *
	 * 2017-01-13
	 * Задействовал @uses df_con_hier(), чтобы подхватывать @see \Df\StripeClone\Block\Info
	 * для потомков @see @see \Df\StripeClone\Method
	 *
	 * @return string
	 *
	 * 2017-02-08
	 * @see \Dfe\AllPay\Method::getInfoBlockType()
	 * @see \Dfe\CheckoutCom\Method::getInfoBlockType()
	 */
	function getInfoBlockType() {return df_con_hier($this, \Df\Payment\Block\Info::class);}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's getInfoInstance() used? https://mage2.pro/t/696
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L210-L218
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L531-L545
	 * @throws DFE
	 * @return II|I|OP|QP
	 *
	 * 2017-02-09
	 * Раньше (почти ровно год назад) я сделал реализацию этого метода по аналогии с
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getInfoInstance()
	 * Т.е. мы падали, если @see _ii ещё не была инициализирована.
	 * Это весь год работало нормально и нас устраивало.
	 * Однако теперь наши сценарии использоваться этого класса стали более сложными и разнообразными.
	 * В частности, совсем недавно (2 дня назад) появилась функция @see dfpm(),
	 * где @see _ii мы стали устанавливать вручную:
	 * https://github.com/mage2pro/core/blob/1.12.13/Payment/lib/method.php?ts=4#L15
	 * А теперь, всего через 2 дня, мы столкнулись с тем, что реализация
	 * @see \Dfe\Stripe\Method::amountLimits() требует наличия _ii,
	 * в то время как этот метод вызывается до инициализации ядром _ii,
	 * из \Magento\Payment\Model\MethodList::getAvailableMethods()
	 * Это происходит, например, при переходе из корзины к оформлению заказа.
	 * В этом сценарии мы вполне можем инициализировать _ii вручную
	 * аналогично уже упомянутой выше функции @see dfpm(),
	 * т.е. просто используя текущую корзину.
	 * А ядро уже затем, если ему нужно, вызовет @see setInfoInstance() повторно.
	 *
	 * 2017-02-11
	 * @used-by \Df\Payment\TM::__construct()
	 * @used-by \Df\Payment\Facade::ii()
	 */
	final function getInfoInstance() {
		if (!$this->_ii && ($q = df_quote())) {
			/** @var Q $q */
			$this->setInfoInstance(dfp($q));
		}
		return $this->_ii ?: df_error('We cannot retrieve the payment information object instance.');
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
	 *
	 * 2016-09-07
	 * Для самого себя я использую метод @see store()
	 */
	final function getStore() {return $this->_storeId;}

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
	final function getTitle() {return dfc($this, function() {return df_is_backend() ? $this->titleB() :
		$this->s('title', null, function() {return df_class_second($this);})
	;});}

	/**
	 * 2016-03-06
	 * @used-by \Df\Payment\Init\Action::action()
	 * @param string|null $k [optional]
	 * @return II|I|OP|QP|mixed
	 */
	final function ii($k = null) {return dfak($this->getInfoInstance(), $k);}

	/**
	 * 2016-03-06
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by \Df\Payment\PlaceOrderInternal::setData()
	 * @param string|array(string => mixed) $k [optional]
	 * @param mixed|null $v [optional]
	 * @return void
	 */
	final function iiaSet($k, $v = null) {$this->ii()->setAdditionalInformation($k, $v);}

	/**
	 * 2016-09-01
	 * 2017-01-13
	 * @used-by \Df\GingerPaymentsBase\Init\Action::res()
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by \Df\StripeClone\Method::transInfo()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * Эта информация в настоящее время используется:
	 *
	 * 1) Для показа её на административном экране транзакции:
	 * https://site.com/admin/sales/transactions/view/txn_id/347/order_id/354/
	 * Она извлекается и обрабатывается в методе
	 * @see \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
	 *
	 * 2) Для показа её в витринном и административном блоках информации о платеже.
	 *
	 * Раньше я конвертировал массивы в JSON перед записью.
	 * Теперь я это стал делать непосредственно перед отображением: так надёжнее,
	 * потому что ранее я порой ненароком забывал сконвертировать какой-нибудь массив в JSON
	 * перед записью, и при отображении это приводило к сбою «array to string conversion».
	 *
	 * @param string|array(string => mixed)|null $req
	 * @param string|array(string => mixed)|null $res
	 */
	final function iiaSetTRR($req, $res = null) {df_trd_set($this->ii(),
		df_clean([self::IIA_TR_REQUEST => $req, self::IIA_TR_RESPONSE => $res])
		+ $this->ii()->getTransactionAdditionalInfo(T::RAW_DETAILS)
	);}

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
	 * @return void
	 * 
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::initialize()
	 */
	function initialize($paymentAction, $stateObject) {}

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
	final function isActive($storeId = null) {return $this->s()->b('enable', $storeId);}

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
	 * @param CartInterface|Q $quote [optional]
	 * @return bool
	 */
	final function isAvailable(CartInterface $quote = null) {
		/** @var bool $result */
		if ($result = ($this->availableInBackend() || !df_is_backend())
			&& $this->isActive($quote ? $quote->getStoreId() : null)
		) {
			/** @var DataObject $checkResult */
			df_dispatch('payment_method_is_active', ['method_instance' => $this, 'quote' => $quote,
				'result' => ($checkResult = new DataObject(['is_available' => true]))
			]);
			$result = $checkResult['is_available'];
		}
		// 2017-02-08
		// Допустимы следующие форматы $limits:
		// 1) null или [] — отсутствие лимитов.
		// 2) [min, max] — общие лимиты для всех валют
		// 3) \Closure — лимиты вычисляются динамически для конкретной валюты
		// 4) ['USD' => [min, max], '*' => [min, max]] — лимиты заданы с таблицей,
		// причём '*' — это лимиты по умолчанию.
		/** @var null|[]|\Closure|array(int|float|null)|array(string => array(int|float|null)) $limits */
		if ($result && $quote && ($limits = $this->amountLimits())) {
			/** @var float $amount */
			$amount = $this->s()->cFromBase($quote->getBaseGrandTotal(), $quote);
			/** @var string $currencyC */
			$currencyC = $this->s()->currencyC($quote);
			/** @var null|array(int|float|null) $limitsForCurrency */
			if ($limitsForCurrency = $limits instanceof \Closure ? $limits($currencyC) : (
				!df_is_assoc($limits) ? $limits : dfa($limits, $currencyC, dfa($limits, '*'))
			)) {
				/** @var int|float|null $min */
				/** @var int|float|null $max */
				list($min, $max) = $limitsForCurrency;
				$result = (is_null($min) || $amount >= $min) && (is_null($max) || $amount <= $max);
			}
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
	final function isGateway() {return false;}

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
	 *
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::isInitializeNeeded()
	 */
	function isInitializeNeeded() {return false;}

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
	final function isOffline() {return false;}

	/**
	 * 2016-03-15
	 * @used-by \Df\Payment\Init\Action::o()
	 * @used-by \Dfe\TwoCheckout\Method::charge()
	 * @return O
	 */
	final function o() {return df_order($this->ii());}

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
	 * @return void
	 */
	final function order(II $payment, $amount) {df_should_not_be_here();}

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
	final function refund(II $payment, $amount) {
		df_cm_set_increment_id($this->ii()->getCreditmemo());
		/** @uses \Df\Payment\Method::_refund() */
		$this->action('_refund', $this->cFromBase($amount));
		return $this;
	}

	/**
	 * 2016-07-13
	 * 2016-11-13
	 * Значение параметра $s сюда, как правило, передавать нет необходимости,
	 * потому что оно инициализируется в @see setStore()
	 * 2017-02-08
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\Payment\Init\Action::s()
	 * @param string|null $k [optional]
	 * @param null|string|int|ScopeInterface $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return Settings|mixed
	 */
	function s($k = null, $s = null, $d = null) {
		/** @var Settings $r */
		$r = dfc($this, function($storeId) {return
			Settings::convention(static::class)->setScope($storeId)
		;}, [$s ? df_store_id($s) : $this->getStore()]);
		return is_null($k) ? $r : $r->v($k, null, $d);
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
	 *
	 * 2017-02-08
	 * @used-by getInfoInstance()
	 */
	final function setInfoInstance(II $info) {
		$this->_ii = $info;
		/**
		 * 2017-03-14
		 * Сюда мы попадаем, в частности, из:
		 * 1) @used-by \Magento\Quote\Model\Quote\Payment::getMethodInstance()
		 * 2) @used-by \Magento\Sales\Model\Order\Payment::getMethodInstance()
		 * Метод №1 устанавливает платёжному методу магазин,
		 * в то время как метод №2 — не устанавливает.
		 * По этой причине, если $info имеет класс @see \Magento\Sales\Model\Order\Payment,
		 * то устанавливаем магазин платёжному методу вручную.
		 */
		if ($info instanceof OP) {
			$this->setStore($info->getOrder()->getStoreId());
		}
	}

	/**
	 * 2016-02-09
	 * @override
	 * How is a payment method's setStore() used? https://mage2.pro/t/693
	 * @see \Magento\Payment\Model\MethodInterface::setStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L42-L47
	 * @see \Magento\Payment\Model\Method\AbstractMethod::setStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L270-L276
	 *
	 * 2017-01-11
	 * Заманчиво было бы здесь, в единой точке, устанавливать в Sentry
	 * общую для всех платёжных операций модуля информацию:
	 * *) название платёжного модуля
	 * *) режим работы платёжного модуля (тестовый или промышленный)
	 * *) версия платёжного модуля.
	 * Однако в Magento присутствуют сценарии
	 * (к ним относится и главный сценарий: оплаты заказа с витрины),
	 * когда платёжные модули инициализируются пакетно, скопом.
	 * Поэтому общую инициализацию Sentry мы размещаем не здесь,
	 * а непосредственно перед платёжной операцией: @see action()
	 *
	 * 2017-03-14
	 * @used-by setInfoInstance()
	 *
	 * @param int $storeId
	 * @return void
	 */
	final function setStore($storeId) {$this->s()->setScope($this->_storeId = (int)$storeId);}

	/**
	 * 2017-01-22
	 * Первый аргумент — для тестового режима, второй — для промышленного.
	 * @param mixed[] ...$args [optional]
	 * @return bool|mixed
	 */
	final function test(...$args) {return df_b($args, $this->s()->test());}

	/**
	 * 2017-03-22
	 * @used-by \Df\Payment\Init\Action::e2i()
	 * @used-by \Df\PaypalClone\W\Nav::e2i()
	 * @used-by \Df\StripeClone\Method::e2i()
	 * @used-by \Df\StripeClone\Method::i2e()
	 * @used-by \Df\StripeClone\W\Nav::e2i()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 * @return TID
	 */
	final function tid() {return TID::s($this);}

	/**
	 * 2016-08-20
	 * @used-by \Df\Payment\Observer\FormatTransactionId::execute()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @param T $t
	 * @return string
	 */
	final function tidFormat(T $t) {return df_tag_if($t->getTxnId(), $url = $this->transUrl($t), 'a', [
		/** @var string|null $url */
		'href' => $url, 'target' => '_blank', 'title' => __(
			'View the transaction in the %1 interface', $this->getTitle()
		)
	]);}

	/**
	 * 2017-01-13
	 * @used-by dfpm_title()
	 * @used-by action()
	 * @used-by getTitle()
	 * @used-by \Df\GingerPaymentsBase\Charge::pClient()
	 * @used-by \Df\Payment\Block\Info::titleB()
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @return string
	 */
	final function titleB() {return self::titleBackendS();}

	/**
	 * 2016-07-28
	 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
	 * @see \Dfe\AllPay\Method::titleDetailed()
	 * @return string
	 */
	function titleDetailed() {return $this->getTitle();}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's validate() used? https://mage2.pro/t/698
	 * @see \Magento\Payment\Model\MethodInterface::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L230-L237
	 * @see \Magento\Payment\Model\Method\AbstractMethod::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L566-L583
	 * @throws LE
	 * @return $this
	 */
	final function validate() {
		if (!$this->canUseForCountry(dfp_oq($this->ii())->getBillingAddress()->getCountryId())) {
			throw new LE(__(
				'You can\'t use the payment type you selected to make payments to the billing country.'
			));
		}
		if ($this->test()) {
			$this->iiaSet(self::II__TEST, true);
		}
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
	 * @uses _void()
	 */
	final function void(II $payment) {
		$this->action('_void');
		/**
		 * 2017-01-17
		 * В @see \Df\Payment\Observer\Void мы закрываем заказ,
		 * и там объяснено, почему мы не можем этого делать здесь.
		 */
		return $this;
	}

	/**
	 * 2016-08-14
	 * @used-by refund()
	 * @used-by _void()
	 * @see \Df\StripeClone\Method::_refund()
	 * @see \Dfe\SecurePay\Method::_refund()
	 * @see \Dfe\TwoCheckout\Method::_refund()
	 * @param float $amount
	 * @return void
	 */
	protected function _refund($amount) {}

	/**
	 * 2016-08-14
	 * @used-by \Df\Payment\Method::void()
	 * @see \Dfe\CheckoutCom\Method::_void()
	 */
	protected function _void() {$this->_refund($this->cFromBase($this->ii()->getBaseAmountAuthorized()));}

	/**
	 * 2016-11-13
	 * @used-by \Df\Payment\Method::amountFormat()
	 * @used-by \Df\Payment\Method::amountParse()
	 * @see \Dfe\AllPay\Method::amountFactor()
	 * @see \Dfe\TwoCheckout\Method::amountFactor()
	 * @return int
	 */
	protected function amountFactor() {return df_find(function($factor, $list) {return
		'*' === $list || in_array($this->cPayment(), is_array($list) ? $list : df_csv_parse($list))
	;}, $this->amountFactorTable(), [], [], DF_BEFORE) ?: 100;}

	/**
	 * 2016-11-13
	 * @used-by \Df\Payment\Method::amountFactor()
	 * @see \Dfe\CheckoutCom\Method::amountFactor()
	 * @see \Dfe\Stripe\Method::amountFactorTable()
	 * @return array(int => string|string[])
	 */
	protected function amountFactorTable() {return [];}

	/**
	 * 2016-02-29
	 * Решил, что значением по умолчанию разумно сделать false.
	 * @used-by \Df\Payment\Method::isAvailable()
	 * @return bool
	 */
	final protected function availableInBackend() {return false;}

	/**
	 * 2016-03-08
	 * @used-by \Df\Payment\Method::getConfigData()
	 * @return string
	 */
	final protected function cardTypes() {return $this->s('cctypes');}

	/**
	 * 2016-08-14
	 * 2017-01-06
	 * Назначение этого метода:
	 * 1) выполнить платёжную транзакцию на стороне платёжной системы
	 * 2) задокументировать текущую транзакцию:
	 * 2.1) присвоить ей идентификатор
	 * 2.2) привязать её к родительской транзакции
	 * 2.3) присвоить ей ответ платёжной системы
	 *
	 * Этот метод намеренно ничего не делает для:
	 *
	 * 1) Потомков @see \Df\PaypalClone\Method, потому что
	 * 1.1) задача №1 для этих потомков решается не запросом API к платёжной системе,
	 * а перенаправлением покупателя на платёжную страницу.
	 * 1.2) задача №2 для этих потомков решается не здесь (потому что здесь нет запроса к API),
	 * а в обработчике оповещений от платёжной системы: @see \Df\PaypalClone\W\Handler
	 *
	 * 2) Потомков @see \Df\StripeClone\Method в сценариях обработки оповещений от платёжной системы,
	 * потому что в таких сценариях:
	 * 2.1) задачу №1 выполнять не нужно, ибо на стороне платёжной системы транзакция уже выполнена.
	 * 2.2) задача №2 решается не здесь (потому что здесь нет запроса к API),
	 * а в обработчике оповещений от платёжной системы: @see \Df\StripeClone\W\Handler
	 *
	 * @used-by authorize()
	 * @used-by capture()
	 * @see \Df\StripeClone\Method::charge()
	 * @see \Dfe\CheckoutCom\Method::charge()
	 * @see \Dfe\TwoCheckout\Method::charge()
	 * @see \Dfe\Square\Method::charge()
	 * @param float $amount
	 * @param bool $capture [optional]
	 * @return void
	 */
	protected function charge($amount, $capture = true) {}

	/**
	 * 2016-12-28
	 * 2017-01-10
	 * Назначение этого метода — конвертация исключительных ситуаций
	 * библиотеки платёжной системы в наши.
	 * Исключительные ситуации библиотеки платёжной системы имеют свою внутреннуюю структуру,
	 * да и их диагностические сообщения — это не всегда то, что нам нужно.
	 * По этой причине мы их конвертируем в свои.
	 * Пока данная функциональность используется модулем Stripe.
	 * @used-by action()
	 * @see \Dfe\Stripe\Method::convertException()
	 * @param \Exception $e
	 * @return \Exception
	 */
	protected function convertException(\Exception $e) {return $e;}

	/**
	 * 2016-03-06
	 * @see \Df\Payment\Charge::iia()
	 * @param string[] ...$keys
	 * @return mixed|array(string => mixed)
	 */
	final protected function iia(...$keys) {return dfp_iia($this->ii(), $keys);}

	/**
	 * 2016-07-10
	 * @param array(string => mixed) $values
	 * @return void
	 */
	final protected function iiaAdd(array $values) {dfp_add_info($this->ii(), $values);}

	/**
	 * 2016-05-03
	 * @used-by \Df\Payment\Method::assignData()
	 * @see \Df\GingerPaymentsBase\Method::iiaKeys()
	 * @see \Df\StripeClone\Method::iiaKeys()
	 * @see \Dfe\AllPay\Method::iiaKeys()
	 * @see \Dfe\CheckoutCom\Method::iiaKeys()
	 * @see \Dfe\TwoCheckout\Method::iiaKeys()
	 * @see \Dfe\Square\Method::iiaKeys()
	 * @return string[]
	 */
	protected function iiaKeys() {return [];}

	/**
	 * 2016-08-14
	 * @param string|array(string => mixed) $k [optional]
	 * @param mixed|null $v [optional]
	 * @return void
	 */
	final protected function iiaUnset($k, $v = null) {$this->ii()->unsAdditionalInformation($k, $v);}

	/**
	 * 2017-02-01
	 * До сегодняшнего дня Stripe-подобные модули для каждой платёжной операции
	 * создавали как минимум (не считая webhooks) 3 записи в логах:
	 * 1) Stripe: getConfigPaymentAction
	 * 2) [Stripe] chargeNew
	 * 3) Stripe: capture
	 * №1 и №3 создавались как из @used-by action()
	 * Нам не нужно так много записей для единственной операции,
	 * поэтому добавил сейчас возможность отключать логирование в action().
	 * @used-by action()
	 * @see \Df\StripeClone\Method::needLogActions()
	 * @return bool
	 */
	protected function needLogActions() {return true;}

	/**
	 * 2016-03-15
	 * @return int|null
	 */
	final protected function oi() {return $this->o()->getId();}

	/**
	 * 2016-09-06
	 * @return string
	 */
	final protected function oii() {return $this->o()->getIncrementId();}

	/**
	 * 2016-08-20
	 * @used-by \Df\Payment\Method::tidFormat()
	 * @see \Df\StripeClone\Method::transUrl()
	 * @param T $t
	 * @return string|null
	 */
	protected function transUrl(T $t) {return null;}

	/**
	 * 2016-09-06
	 * @used-by \Df\Payment\Method::cFromBase()
	 * @used-by \Df\Payment\Method::cFromOrder()
	 * @used-by \Df\Payment\Method::cToBase()
	 * @used-by \Df\Payment\Method::cToOrder()
	 * @param float $amount
	 * @return float
	 */
	private function convert($amount) {return call_user_func(
		[$this->s(), df_caller_f()], $amount, $this->o()
	);}

	/**
	 * 2016-09-07
	 * Код валюты заказа.
	 * @used-by \Df\Payment\Method::cFromBase()
	 * @used-by \Df\Payment\Method::cFromOrder()
	 * @used-by \Df\Payment\Method::cPayment()
	 * @return string
	 */
	private function cOrder() {return $this->o()->getOrderCurrencyCode();}

	/**
	 * 2017-02-07
	 * @used-by cPayment()
	 * @return O|Q
	 */
	private function oq() {return dfc($this, function() {return
		$this->ii()->getOrder() ?: $this->ii()->getQuote()
	;});}

	/**
	 * 2016-09-07
	 * Намеренно не используем @see _storeId
	 * @return Store
	 */
	private function store() {return dfc($this, function() {return $this->o()->getStore();});}

	/**
	 * 2016-02-12
	 * @used-by getInfoInstance()
	 * @used-by setInfoInstance()
	 * @var II|I|OP|QP
	 */
	private $_ii;

	/**
	 * 2016-02-09
	 * @used-by getStore()
	 * @used-by setStore()
	 * @var int
	 */
	private $_storeId;

	/**
	 * 2016-07-13
	 * @used-by dfp_is_test()
	 * @used-by validate()
	 */
	const II__TEST = 'df_test';

	/**
	 * 2017-03-22
	 * @used-by iiaSetTRR()
	 * @used-by \Df\Payment\TM::req()
	 */
	const IIA_TR_REQUEST = 'Request';

	/**
	 * 2016-12-29
	 * @used-by iiaSetTRR()
	 * @used-by \Df\StripeClone\Block\Info::responseRecord()
	 */
	const IIA_TR_RESPONSE = 'Response';

	/**
	 * 2016-08-14
	 * 2017-01-06
	 * Установка этого временного флага (флаг присутствует на протяжении обработки
	 * текущего запроса HTTP, но не сохраняется в базе данных)
	 * говорит платёжному модулю о том, что инициатором данной платёжной транзакции
	 * является платёжная система (как правильно — это либо действия работника магазина
	 * в личном кабинете магазина в платёжной системы,
	 * либо асинхронное уведомление платёжной системы о статусе обработки ею платежа,
	 * либо действия покупателя в случае оффлайнового способа оплаты),
	 * а не Magento (не действия покупателя в магазине
	 * и не действия работника магазина в административной части Magento).
	 *
	 * В такой ситуации модуль должен выполнять лишь ту часть платёжной операции,
	 * которая относится к Magento, но модуль не должен запрашивать выполнение этой операции
	 * на стороне платёжной системы, потому что на стороне платёжной системы
	 * эта операция уже выполнена, и платёжная система как раз нас об этом уведомляет.
	 * @used-by action()
	 * @used-by dfp_webhook_case()
	 */
	const WEBHOOK_CASE = 'df_webhook_case';

	/**
	 * 2016-07-10
	 * @used-by dfpm_code()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @see \Dfe\Stripe\Method => «dfe_stripe»
	 * @see \Dfe\CheckoutCom\Method => «dfe_checkout_com»
	 * @return string
	 */
	final static function codeS() {return dfcf(function($class) {return
		df_const($class, 'CODE', function() use($class) {return df_module_name_lc($class);})
	;}, [static::class]);}

	/**
	 * 2016-07-10
	 * @param string $globalId
	 * @return string
	 */
	final static function transactionIdG2L($globalId) {return df_trim_text_left(
		$globalId, self::codeS() . '-'
	);}

	/**
	 * 2016-08-06
	 * 2016-09-04
	 * Используемая конструкция реально работает: https://3v4l.org/Qb0uZ
	 * @used-by titleB()
	 * @return string
	 */
	private static function titleBackendS() {return dfcf(function($class) {return
		Settings::convention($class, 'title_backend', null, function() use($class) {return
			df_class_second($class)
		;})
	;}, [static::class]);}
}