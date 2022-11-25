<?php
namespace Df\Payment;
use Df\Config\Source\NoWhiteBlack as NWB;
use Df\Core\Exception as DFE;
use Df\Core\ICached;
use Df\Payment\Block\Info as bInfo;
use Df\Payment\Init\Action as InitAction;
use Magento\Framework\App\Area;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\DataObject as _DO;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManager\NoninterceptableInterface as INonInterceptable;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface as ICart;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
use \Exception as E;
/**
 * 2016-02-08
 * 2017-03-30
 * Каждый потомок Method является объектом-одиночкой: @see \Df\Payment\Method::sg(),
 * но вот info instance в него может устанавливаться разный: @see \Df\Payment\Method::setInfoInstance()
 * Так происходит, например, в методе @see \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * https://github.com/mage2pro/core/blob/2.4.13/Payment/Observer/DataProvider/SearchResult.php#L52-L65
 * Аналогично, в Method может устанавливаться разный store: @see \Df\Payment\Method::setStore()
 * Поэтому будьте осторожны с кэшированием внутри Method!
 * 2017-11-11
 * I disable the `\Interceptor` target classes generation for this source class and all its descendants.
 * "How to disable the `\Interceptor` target class generation for a particular source class?"
 * https://mage2.pro/t/4914
 * I useit to overcome issues like:
 * 1) «Class Dfe\IPay88\Method\Interceptor may not inherit from final class (Dfe\IPay88\Method)
 * in generated/code/Dfe/IPay88/Method/Interceptor.php on line 7:
 * https://mage2.pro/t/4904
 * 2) «Class Dfe\TwoCheckout\Method\Interceptor may not inherit from final class (Dfe\TwoCheckout\Method)»
 * https://mage2.pro/t/4892
 * 3) «Cannot call private Dfe\IPay88\Method::__construct()
 * in generated/code/Dfe/IPay88/Method/Interceptor.php» https://mage2.pro/t/topic/4913
 * @see \Df\GingerPaymentsBase\Method
 * @see \Df\PaypalClone\Method
 * @see \Df\StripeClone\Method
 * @see \Dfe\ACH\Method
 * @see \Dfe\CheckoutCom\Method
 * @see \Dfe\Klarna\Method
 * @see \Dfe\Qiwi\Method
 * @see \Dfe\TwoCheckout\Method
 */
abstract class Method implements ICached, INonInterceptable, MethodInterface {
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
	 * @used-by self::isAvailable()
	 * @see \Df\GingerPaymentsBase\Method::amountLimits()
	 * @see \Dfe\ACH\Method::amountLimits()
	 * @see \Dfe\AllPay\Method::amountLimits()
	 * @see \Dfe\AlphaCommerceHub\Method::amountLimits()
	 * @see \Dfe\CheckoutCom\Method::amountLimits()
	 * @see \Dfe\Dragonpay\Method::amountLimits()
	 * @see \Dfe\IPay88\Method::amountLimits()
	 * @see \Dfe\Klarna\Method::amountLimits()
	 * @see \Dfe\Moip\Method::amountLimits()
	 * @see \Dfe\Omise\Method::amountLimits()
	 * @see \Dfe\Paymill\Method::amountLimits()
	 * @see \Dfe\PostFinance\Method::amountLimits()
	 * @see \Dfe\Qiwi\Method::amountLimits()
	 * @see \Dfe\Robokassa\Method::amountLimits()
	 * @see \Dfe\SecurePay\Method::amountLimits()
	 * @see \Dfe\Spryng\Method::amountLimits()
	 * @see \Dfe\Square\Method::amountLimits()
	 * @see \Dfe\Stripe\Method::amountLimits()
	 * @see \Dfe\TwoCheckout\Method::amountLimits()
	 * @see \Dfe\Vantiv\Method::amountLimits()
	 * @see \Dfe\YandexKassa\Method::amountLimits()
	 * @return null|[]|\Closure|array(int|float|null)|array(string => array(int|float|null))
	 */
	abstract protected function amountLimits();

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's acceptPayment() used? https://mage2.pro/t/715
	 * @see \Magento\Payment\Model\MethodInterface::acceptPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L304-L312
	 * @see \Magento\Payment\Model\Method\AbstractMethod::acceptPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L696-L713
	 * 2016-05-09
	 * A «Flagged» payment can be handled the same way as an «Authorised» payment: we can «capture» or «void» it.
	 * @param II|I|OP $p
	 */
	final function acceptPayment(II $p):bool {
		# 2016-03-15
		# The obvious `$this->charge($payment)` is not quite correct, because no invoice will be created in such case.
		$p->capture();
		return true;
	}

	/**
	 * 2016-08-14
	 * 2017-01-10
	 * Этот метод служит единой точкой входа для всех платёжных транзакций нашего класса.
	 * Сведение их в единую точку позволяет нам централизованно:
	 * 1) Отфлильтровывать случаи выполнения транзакций из webhooks
	 * (в этом случае мы не обращаемся к API платёжной системы,
	 * потому что на стороне платёжной системы транзакция уже проведена, о чём мы и получили оповещение в webhook).
	 * 2) Обрабатывать исключительные ситуации.
	 * При этом каждый платёжный модуль может иметь свои индивидуальные особенности
	 * обработки исключительных ситуаций, а здесь мы лишь выполняем общую, универсальную часть такой обработки.
	 * 3) Инициализировать библиотеку платёжной системы.
	 * @used-by self::authorize()
	 * @used-by self::capture()
	 * @used-by self::refund()
	 * @used-by self::void()
	 * @used-by \Df\Payment\Init\Action::action()
	 * @param string|\Closure $f
	 * @return mixed
	 */
	final function action($f, bool $log = true) {
		$result = null; /** @var mixed $result */
		if (!$this->ii(self::WEBHOOK_CASE)) {
			dfp_sentry_tags($this);
			df_sentry_tags($this, ['Payment Action' => $actionS = df_caller_f()]); /** @var string $actionS */
			try {
				$this->s()->init();
				# 2017-01-10 Такой код корректен, проверял: https://3v4l.org/Efj63
				$result = call_user_func($f instanceof \Closure ? $f : [$this, $f]);
				/**
				 * 2017-01-31
				 * В настоящее время опция «Log the API requests and responses?»
				 * присутствует у модулей allPay и SecurePay:
				 * 		1) allPay: https://github.com/mage2pro/allpay/blob/1.1.25/etc/adminhtml/system.xml?ts=4#L413-L426
				 * 		2) SecurePay: https://github.com/mage2pro/securepay/blob/1.1.17/etc/adminhtml/system.xml?ts=4#L156-L169
				 * У остальных моих платёжных модулей этой опции пока нет,
				 * там функциональность логирования пока включена намертво.
				 * 2017-02-01
				 * До сегодняшнего дня Stripe-подобные модули для каждой платёжной операции
				 * создавали как минимум (не считая webhooks) 3 записи в логах:
				 * 		1) Stripe: getConfigPaymentAction
				 * 		2) [Stripe] chargeNew
				 * 		3) Stripe: capture
				 * №1 и №3 создавались как раз отсюда, из action()
				 * Нам не нужно так много записей для единственной операции,
				 * поэтому добавил сейчас возможность отключать логирование в action().
				 */
				if ($log && $this->needLogActions() && $this->s()->log()) {
					df_sentry($this, "{$this->titleB()}: $actionS");
				}
			}
			catch (E $e) {
				# 2017-01-10
				# Конвертация исключительных ситуаций библиотеки платёжной системы в наши.
				# Исключительные ситуации библиотеки платёжной системы имеют свою внутреннуюю структуру,
				# да и их диагностические сообщения — это не всегда то, что нам нужно.
				# По этой причине мы их конвертируем в свои.
				# Пока данная функциональность используется модулем Stripe.
				df_log($e = $this->convertException($e));
				/**
				 * 2016-03-17
				 * Чтобы система показала наше сообщение вместо общей фразы типа
				 * «We can't void the payment right now», надо вернуть объект именно класса
				 * @uses \Magento\Framework\Exception\LocalizedException
				 * https://mage2.pro/t/945
				 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Controller/Adminhtml/Order/VoidPayment.php#L20-L30
				 * 2017-09-27
				 * Previously, I had the following code here: throw df_lx($e);
				 * It triggered a false positive of the Magento Marketplace code validation tool:
				 * «Namespace for df_le class is not specified»:
				 * https://github.com/mage2pro/core/issues/27
				 * https://github.com/magento/marketplace-eqp/issues/45
				 * So I write it in the 2 lines as a workaround: $e = df_lx($e); throw $e;
				 * 2022-11-10 @noinspection PhpUnnecessaryLocalVariableInspection
				 */
				$e = df_lx($e); throw $e;
			}
		}
		return $result;
	}

	/**
	 * 2016-09-07
	 * Конвертирует денежную величину (в валюте платежа) из обычного числа в формат платёжной системы.
	 * В частности, некоторые платёжные системы хотят денежные величины в копейках (Checkout.com),
	 * обязательно целыми (allPay) и т.п.
	 * 2016-09-08 Эта операция является обратной по отношению к @see amountParse()
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\Operation::amountFormat()
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @used-by \Df\StripeClone\Method::charge()
	 * @see \Dfe\AlphaCommerceHub\Method::amountFormat()
	 * @see \Dfe\Dragonpay\Method::amountFormat()
	 * @see \Dfe\IPay88\Method::amountFormat()
	 * @see \Dfe\Qiwi\Method::amountFormat()
	 * @see \Dfe\Robokassa\Method::amountFormat()
	 * @see \Dfe\TwoCheckout\Method::amountFormat()
	 * @see \Dfe\YandexKassa\Method::amountFormat()
	 * @return float|int|string
	 */
	function amountFormat(float $a) {return round($a * $this->amountFactor());}

	/**
	 * 2016-09-08
	 * Конвертирует денежную величину из формата платёжной системы в обычное число.
	 * Обратная операция по отношению к @see amountFormat()
	 * @used-by dfp_refund()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::btInstructions()
	 * @used-by \Dfe\Stripe\Method::amountLimits()
	 * @param float|int|string $a
	 */
	final function amountParse($a):float {return $a / $this->amountFactor();}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's assignData() used? https://mage2.pro/t/718
	 * @see \Magento\Payment\Model\MethodInterface::assignData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L304-L312
	 * @see \Magento\Payment\Model\Method\AbstractMethod::assignData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L762-L797
	 * ISSUES with @see \Magento\Payment\Model\Method\AbstractMethod::assignData():
	 * 1) The @see \Magento\Payment\Model\Method\AbstractMethod::assignData() method
	 * can be simplified: https://mage2.pro/t/719
	 * 2) The @see \Magento\Payment\Model\Method\AbstractMethod::assignData() method
	 * has a wrong PHPDoc declaration: https://mage2.pro/t/720
	 * 2022-10-19
	 * We can use `final` in the method's signature despite if M2 code generation
	 * because the class implements @see INonInterceptable.
	 * @used-by self::isAvailable()
	 */
	final function assignData(_DO $o):self {
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
		$iia = $o['additional_data'] ?: $o->getData();
		foreach ($this->iiaKeys() as $k) {/** @var string $k */
			$v = dfa($iia, $k); /** @var string|null $v */
			if (!is_null($v)) {
				$this->iiaSet($k, $v);
			}
		}
		/**
		 * 2017-10-12
		 * I have removed the `payment_method_assign_data`
		 * and `payment_method_assign_data_{$this->getCode()}` events triggering, because
		 * 1) I do not use them
		 * 2) The M2 core does not use them for my modules.
		 * It uses it for Vault: @see \Magento\Vault\Observer\VaultEnableAssigner::execute()
		 * But I do not use the core's Vault.
		 * 3) Now I call assignData() one more time manually: @see \Df\Payment\Method::isAvailable(),
		 * and I do not want the same events were triggered mutiple times.
		 * The removed code is here:
		 * https://github.com/mage2pro/core/blob/3.1.1/Payment/Method.php#L286-L300
		 *	df_dispatch("payment_method_assign_data_{$this->getCode()}", $eventParams = [
		 *		AssignObserver::METHOD_CODE => $this,
		 *		'payment_model' => $this->ii(),
		 *		AssignObserver::DATA_CODE => $data
		 *	]);
		 *	df_dispatch('payment_method_assign_data', $eventParams);
		 */
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::authorize()
	 * @used-by \Magento\Sales\Model\Order\Payment\Operations\AuthorizeOperation::authorize()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Sales/Model/Order/Payment/Operations/AuthorizeOperation.php#L45
	 * How is a payment method's authorize() used? https://mage2.pro/t/707
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * @see \Df\Payment\Currency::iso3()
	 * 2016-08-19
	 * Со вчерашнего для мои платёжные модули выполняют платёжные транзакции
	 * не в учётной валюте системы, а в валюте заказа (т.е., витринной валюте).
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
	 * но вызов $payment->isCaptureFinal($a) вернёт false,
	 * потому что $a — размер платежа в учётной валюте системы, а метод устроен так:
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
	 * Самым разумным решением этой проблемы мне показалось ручное убирание флага IsFraudDetected
	 * 2017-04-08
	 * Отныне аргумент $a намеренно игнорируем с целью упрощения системы,
	 * потому что это значение мы можем получить в любой удобный момент самостоятельно
	 * посредством @see dfp_due()
	 * 2021-07-01
	 * $a is a string because it is a result of the @see \Magento\Sales\Model\Order\Payment::formatAmount() call:
	 * 		$amount = $payment->formatAmount($amount, true);
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment/Operations/AuthorizeOperation.php#L36
	 * 2022-10-19
	 * We can use `final` in the method's signature despite if M2 code generation
	 * because the class implements @see INonInterceptable.
	 * @param string|float $a
	 * В спецификации PHPDoc интерфейса указано, что метод должен возвращать $this,
	 * но реально возвращаемое значение ядром не используется.
	 */
	final function authorize(II $i, $a):self {return $this->action(function() use($i) {
		if ($i instanceof OP) {
			$i->setIsFraudDetected(false);
		}
		$this->charge(false);
		return $this;
	});}

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
	 */
	final function canAuthorize():bool {df_should_not_be_here(); return false;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/tags/capture
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
	 * How is payment method's canCapture() used? https://mage2.pro/t/645
	 * How is @see \Magento\Sales\Model\Order\Payment::canCapture() used? https://mage2.pro/t/650
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
	 * 2017-12-07
	 * 1) @used-by \Magento\Sales\Model\Order\Payment::canCapture():
	 *		if (!$this->getMethodInstance()->canCapture()) {
	 *			return false;
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L246-L269
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment.php#L277-L301
	 * 2) @used-by \Magento\Sales\Model\Order\Payment::_invoice():
	 *		protected function _invoice() {
	 *			$invoice = $this->getOrder()->prepareInvoice();
	 *			$invoice->register();
	 *			if ($this->getMethodInstance()->canCapture()) {
	 *				$invoice->capture();
	 *			}
	 *			$this->getOrder()->addRelatedObject($invoice);
	 *			return $invoice;
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L509-L526
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment.php#L542-L560
	 * 3) @used-by \Magento\Sales\Model\Order\Payment\Operations\AbstractOperation::invoice():
	 *		protected function invoice(OrderPaymentInterface $payment) {
	 *			$invoice = $payment->getOrder()->prepareInvoice();
	 *			$invoice->register();
	 *			if ($payment->getMethodInstance()->canCapture()) {
	 *				$invoice->capture();
	 *			}
	 *			$payment->getOrder()->addRelatedObject($invoice);
	 *			return $invoice;
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment/Operations/AbstractOperation.php#L56-L75
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment/Operations/AbstractOperation.php#L59-L78
	 * @see \Df\StripeClone\Method::canCapture()
	 * @see \Dfe\AlphaCommerceHub\Method::canCapture()
	 * @see \Dfe\CheckoutCom\Method::canCapture()
	 * @see \Dfe\TwoCheckout\Method::canCapture()
	 */
	function canCapture():bool {return df_area_code_is(Area::AREA_FRONTEND, Area::AREA_WEBAPI_REST);}

	/**
	 * 2016-02-10
	 * @override
	 * https://mage2.pro/tags/capture
	 * https://mage2.pro/t/658
	 * The @see \Magento\Payment\Model\MethodInterface::canCaptureOnce() is never used
	 * @see \Magento\Payment\Model\MethodInterface::canCaptureOnce()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L87-L93
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canCaptureOnce()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L330-L339
	 */
	final function canCaptureOnce():bool {df_should_not_be_here(); return false;}

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
	 * @see \Dfe\TBCBank\Method::canCapturePartial()
	 */
	function canCapturePartial():bool {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * "How is a payment method's cancel() used?" https://mage2.pro/t/710
	 * @see \Magento\Payment\Model\MethodInterface::cancel()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L279-L286
	 * @see \Magento\Payment\Model\Method\AbstractMethod::cancel()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L658-L669
	 * 2022-10-19
	 * We can use `final` in the method's signature despite if M2 code generation
	 * because the class implements @see INonInterceptable.
	 */
	final function cancel(II $payment):self {return $this;}

	/**
	 * 2016-02-10
	 * @override
	 * "How is a payment method's canEdit() used?" https://mage2.pro/t/672
	 * "How is \Magento\Sales\Model\Order::canEdit() implemented and used?" https://mage2.pro/t/673
	 * @see \Magento\Payment\Model\MethodInterface::canEdit()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L133-L139
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canEdit()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L395-L404
	 */
	final function canEdit():bool {return true;}

	/**
	 * 2016-02-11
	 * @override
	 * https://mage2.pro/tags/payment-transaction
	 * @see \Magento\Payment\Model\MethodInterface::canFetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L141-L147
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canFetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L406-L415
	 * USAGES
	 * https://mage2.pro/t/676
	 * How is a payment method's canFetchTransactionInfo() used?
	 * How is @see \Magento\Sales\Model\Order\Payment::canFetchTransactionInfo() implemented and used?
	 * https://mage2.pro/t/677
	 */
	final function canFetchTransactionInfo():bool {return false;}

	/**
	 * 2016-02-09
	 * @override
	 * "The method canOrder() should be removed from the interface \Magento\Payment\Model\MethodInterface
	 * because it is not used outside of a particular interface's implementation" https://mage2.pro/t/640
	 * @see \Magento\Payment\Model\Method\AbstractMethod
	 * @see \Magento\Payment\Model\MethodInterface::canOrder()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L55-L61
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canOrder()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L286-L295
	 */
	final function canOrder():bool {df_should_not_be_here(); return false;}

	/**
	 * 2016-02-10
	 * @override
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат оплаты покупателю.
	 * https://mage2.pro/tags/refund
	 * @see \Magento\Payment\Model\MethodInterface::canRefund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L95-L101
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canRefund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L341-L350
	 * USAGES
	 * "How is a payment method's canRefund() used?" https://mage2.pro/t/659
	 * 2017-12-06
	 * 1) @used-by \Magento\Sales\Model\Order\Payment::canRefund():
	 *		public function canRefund() {
	 *			return $this->getMethodInstance()->canRefund();
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L271-L277
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment.php#L303-L309
	 * 2) @used-by \Magento\Sales\Model\Order\Payment::refund()
	 *		$gateway = $this->getMethodInstance();
	 *		$invoice = null;
	 *		if ($gateway->canRefund()) {
	 *			<...>
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L617-L654
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment.php#L655-L698
	 * 3) @used-by \Magento\Sales\Model\Order\Invoice\Validation\CanRefund::canPartialRefund()
	 *		private function canPartialRefund(MethodInterface $method, InfoInterface $payment) {
	 *			return $method->canRefund() &&
	 *			$method->canRefundPartialPerInvoice() &&
	 *			$payment->getAmountPaid() > $payment->getAmountRefunded();
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Invoice/Validation/CanRefund.php#L84-L94
	 * It is since Magento 2.2: https://github.com/magento/magento2/commit/767151b4
	 * @see \Df\StripeClone\Method::canRefund()
	 * @see \Dfe\AlphaCommerceHub\Method::canRefund()
	 * @see \Dfe\CheckoutCom\Method::canRefund()
	 * @see \Dfe\SecurePay\Method::canRefund()
	 * @see \Dfe\TwoCheckout\Method::canRefund()
	 */
	function canRefund():bool {return false;}

	/**
	 * 2016-02-10
	 * @override
	 * https://mage2.pro/tags/refund
	 * @see \Magento\Payment\Model\MethodInterface::canRefundPartialPerInvoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L103-L109
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canRefundPartialPerInvoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L352-L361
	 * USAGES
	 * "How is a payment method's canRefundPartialPerInvoice() used?" https://mage2.pro/t/663
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::canRefundPartialPerInvoice()
	 * @see \Dfe\CheckoutCom\Method::canRefundPartialPerInvoice()
	 * @see \Dfe\TwoCheckout\Method::canRefundPartialPerInvoice()
	 */
	function canRefundPartialPerInvoice():bool {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * "How is a payment method's canReviewPayment() used?" https://mage2.pro/t/714
	 * 2016-03-08
	 * http://stackoverflow.com/a/12814128
	 * «Magento's Order View block will check $order->canReviewPayment()
	 * which will look at the _canReviewPayment variable on the payment method,
	 * and if true, display two buttons on the Order View :
	 * "Accept Payment" and "Deny Payment".»
	 * @see \Magento\Payment\Model\MethodInterface::canReviewPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L297-L302
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canReviewPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L688-L696
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::canReviewPayment()
	 * @see \Dfe\CheckoutCom\Method::canReviewPayment()
	 */
	function canReviewPayment():bool {return false;}

	/**
	 * 2016-02-10
	 * @override
	 * The same as @see \Df\Payment\Method::canUseInternal(), but it is used for the frontend only.
	 * https://mage2.pro/t/671
	 * https://mage2.pro/tags/payment-can-use
	 * @see \Magento\Payment\Model\MethodInterface::canUseCheckout()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L126-L131
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseCheckout()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L156-L161
	 */
	final function canUseCheckout():bool {return true;}

	/**
	 * 2016-02-11
	 * 2017-03-04
	 * Этот метод решает, должен ли способ оплаты быть доступен для конкретной страны покупателя.
	 * Страна покупателя вычисляется методом
	 * @see \Magento\Payment\Model\Checks\CanUseForCountry\CountryProvider::getCountry()
	 *		public function getCountry(Quote $quote)
	 *		{
	 *			$address = $quote->getBillingAddress() ? : $quote->getShippingAddress();
	 *			return (!empty($address) && !empty($address->getCountry()))
	 *				? $address->getCountry()
	 *				: $this->directoryHelper->getDefaultCountry();
	 *		}
	 * https://github.com/magento/magento2/blob/58edd7f2/app/code/Magento/Payment/Model/Checks/CanUseForCountry/CountryProvider.php#L33-L45
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::canUseForCountry()
	 * @used-by \Df\Payment\Plugin\Model\Checks\CanUseForCountry::aroundIsApplicable()
	 * @used-by \Magento\Payment\Model\Checks\CanUseForCountry::isApplicable()
	 * How is a payment method's canUseForCountry() used? https://mage2.pro/t/682
	 */
	final function canUseForCountry(string $c):bool {return $this->canUseForCountryP($c);}
	
	/**
	 * 2017-12-13
	 * "Provide an ability to the Magento backend users (merchants) to set up country restrictions separately
	 * for each AlphaCommerceHub's payment option (bank cards, PayPal, POLi Payments, etc.)":
	 * https://github.com/mage2pro/alphacommercehub/issues/85
	 * @used-by self::canUseForCountry()
	 * @used-by \Df\Payment\Settings::applicableForQuoteByCountry()
	 * @param string|null $p [optional]
	 */
	final function canUseForCountryP(string $c, $p = null):bool {
		$p = !$p ? $p : df_add_ds_right($p);
		return NWB::is($this->s("{$p}country_restriction"), $c, df_csv_parse($this->s("{$p}countries")));
	}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's canUseForCurrency() used? https://mage2.pro/t/684
	 * @see \Magento\Payment\Model\MethodInterface::canUseForCurrency()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L192-L199
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseForCurrency()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L484-L494
	 * @param string $c
	 */
	final function canUseForCurrency($c):bool {return true;}

	/**
	 * 2016-02-10
	 * @override
	 * Place in your custom canUseInternal() method a custom logic to decide
	 * whether the payment method need to be shown to a customer on the checkout screen.
	 * By default there is no custom login and the method just returns true.
	 * https://mage2.pro/t/670
	 * https://mage2.pro/tags/payment-can-use
	 * @see \Magento\Payment\Model\MethodInterface::canUseInternal()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L118-L124
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseInternal()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L149-L154
	 */
	final function canUseInternal():bool {return true;}

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
	 *
	 * USAGES
	 * "How is a payment method's canVoid() used?" https://mage2.pro/t/666
	 * How is @see \Magento\Sales\Model\Order\Payment::canVoid() implemented and used?
	 * https://mage2.pro/t/667
	 *
	 * 2017-12-08
	 * @used-by \Magento\Sales\Model\Order\Payment::canVoid():
	 *		public function canVoid() {
	 *			if (null === $this->_canVoidLookup) {
	 *				$this->_canVoidLookup = (bool)$this->getMethodInstance()->canVoid();
	 *				if ($this->_canVoidLookup) {
	 *					$authTransaction = $this->getAuthorizationTransaction();
	 *					$this->_canVoidLookup = (bool)$authTransaction && !(int)$authTransaction->getIsClosed();
	 *				}
	 *			}
	 *			return $this->_canVoidLookup;
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L528-L543
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment.php#L562-L578
	 * @see \Df\StripeClone\Method::canVoid()
	 * @see \Dfe\AlphaCommerceHub\Method::canVoid()
	 * @see \Dfe\CheckoutCom\Method::canVoid()
	 */
	function canVoid():bool {return false;}

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
	 * $a содержит значение в учётной валюте системы.
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L37-L37
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L76-L82
	 *
	 * @see \Magento\Payment\Model\MethodInterface::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L259-L267
	 * @see \Magento\Payment\Model\Method\AbstractMethod::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L621-L638
	 *
	 * 2017-04-08
	 * Отныне аргумент $a намеренно игнорируем с целью упрощения системы,
	 * потому что это значение мы можем получить в любой удобный момент самостоятельно
	 * посредством @see dfp_due()
	 *
	 * 2021-07-01
	 * $a is a string because it is a result of the @see \Magento\Sales\Model\Order\Payment::formatAmount() call:
	 * 		$amountToCapture = $payment->formatAmount($invoice->getBaseGrandTotal());
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L37
	 *
	 * 2022-10-19
	 * We can use `final` in the method's signature despite if M2 code generation
	 * because the class implements @see INonInterceptable.
	 *
	 * @param II|I|OP $payment
	 * @param string|float $a
	 * В спецификации PHPDoc интерфейса указано, что метод должен возвращать $this,
	 * но реально возвращаемое значение ядром не используется.
	 * @uses charge()
	 */
	final function capture(II $payment, $a):self {$this->action('charge'); return $this;}

	/**
	 * 2016-08-20
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency»
	 * 2017-02-08 Конвертирует $a из учётной валюты в валюту платежа.
	 * @see \Df\Payment\Currency::iso3()
	 * @used-by dfp_due()
	 * @used-by self::_void()
	 * @used-by self::refund()
	 * @param float $a
	 * @uses \Df\Payment\Currency::fromBase()
	 */
	final function cFromBase($a):float {return $this->convert($a);}

	/**
	 * 2016-09-07 The payment currency code for the current order or quote.
	 * @used-by self::amountFormat()
	 * @used-by \Dfe\AlphaCommerceHub\Method::amountFormat()
	 * @used-by \Dfe\TBCBank\Facade\Charge::capturePreauthorized()
	 */
	final function cPayment():string {return dfc($this, function() {return $this->currency()->oq($this->oq());});}

	/**
	 * 2016-09-08
	 * 2017-02-08 Конвертирует $a из валюты платежа в учётную.
	 * @param float $a
	 * @uses \Df\Payment\Currency::toBase()
	 */
	final function cToBase($a):float {return $this->convert($a);}

	/**
	 * 2016-09-08
	 * 2017-02-08 Конвертирует $a из валюты платежа в валюту заказа.
	 * @param float $a
	 * @uses \Df\Payment\Currency::toOrder()
	 */
	final function cToOrder($a):float {return $this->convert($a);}

	/**
	 * 2016-02-15
	 * @override
	 * "How is a payment method's denyPayment() used?" https://mage2.pro/t/716
	 * @see \Magento\Payment\Model\MethodInterface::denyPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L314-L322
	 * @see \Magento\Payment\Model\Method\AbstractMethod::denyPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L715-L730
	 * @param II|I|OP $p
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::denyPayment()
	 * @see \Dfe\CheckoutCom\Method::denyPayment()
	 */
	function denyPayment(II $p):bool {return false;}

	/**
	 * 2016-02-11
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::fetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L149-L158
	 * @see \Magento\Payment\Model\Method\AbstractMethod::fetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L417-L428
	 * @param II $payment
	 * @param string $transactionId
	 * @return array(string => mixed)
	 * USAGES
	 * "How is a payment method's fetchTransactionInfo() used?" https://mage2.pro/t/678
	 */
	final function fetchTransactionInfo(II $payment, $transactionId):array {return [];}

	/**
	 * 2016-02-08
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L17-L23
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L496-L508
	 */
	final function getCode():string {return self::codeS();}

	/**
	 * 2016-02-15
	 * @override
	 * "How is a payment method's getConfigData() used?" https://mage2.pro/t/717
	 * @see \Magento\Payment\Model\MethodInterface::getConfigData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L324-L332
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getConfigData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L742-L760
	 * @param string $k
	 * @param null|string|int|ScopeInterface $storeId [optional]
	 * @return string|null
	 */
	final function getConfigData($k, $storeId = null) {
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
		return isset($map[$k]) ? call_user_func([$this, $map[$k]], $storeId) : $this->s($k);
	}

	/**
	 * 2016-02-15 How is a payment method's getConfigPaymentAction() used? https://mage2.pro/t/724
	 * 2017-11-06 We really need to cache the result with @uses dfc(), because the method is called 3 times.
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getConfigPaymentAction()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L374-L381
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getConfigPaymentAction()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L854-L864
	 * ----
	 * 1) @used-by \Df\StripeClone\Method::isInitializeNeeded()
	 * 2) @used-by \Magento\Sales\Model\Order\Payment::place()
	 * 		$action = $methodInstance->getConfigPaymentAction();
	 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Sales/Model/Order/Payment.php#L354
	 * 3) @used-by \Magento\Sales\Model\Order\Payment::place()
	 * 		$methodInstance->initialize($methodInstance->getConfigData('payment_action'), $stateObject);
	 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Sales/Model/Order/Payment.php#L359-L360
	 * 		'payment_action' => 'getConfigPaymentAction'
	 * https://github.com/mage2pro/core/blob/3.2.31/Payment/Method.php#L898-L904
	 * ----
	 * @see \Dfe\CheckoutCom\Method::getConfigPaymentAction()
	 */
	function getConfigPaymentAction():string {return dfc($this, function() {return InitAction::sg($this)->action();});}

	/**
	 * 2016-02-08
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getFormBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L25-L32
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getFormBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L510-L518
	 *
	 * USAGE
	 * "How is a payment method's getFormBlockType() used?" https://mage2.pro/t/691
	 * @used-by \Magento\Payment\Helper\Data::getMethodFormBlock()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Helper/Data.php#L174
	 *
	 * 2017-08-24
	 * The method is used in the following scenarios:
	 * 1) Backend ordering
	 * 2) Frontend multishipping
	 *
	 * @used-by \Magento\Payment\Helper\Data::getMethodFormBlock():
	 *	public function getMethodFormBlock(MethodInterface $method, LayoutInterface $layout) {
	 *		$block = $layout->createBlock($method->getFormBlockType(), $method->getCode());
	 *		$block->setMethod($method);
	 *		return $block;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.1/app/code/Magento/Payment/Helper/Data.php#L169-L181
	 *
	 * The @see \Magento\Payment\Helper\Data::getMethodFormBlock() method is used only by
	 * @see \Magento\Payment\Block\Form\Container::_prepareLayout():
	 *	protected function _prepareLayout() {
	 *		foreach ($this->getMethods() as $method) {
	 *			$this->setChild(
	 *				'payment.method.' . $method->getCode(),
	 *				$this->_paymentHelper->getMethodFormBlock($method, $this->_layout)
	 *			);
	 *		}
	 *		return parent::_prepareLayout();
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.1/app/code/Magento/Payment/Block/Form/Container.php#L67-L85
	 */
	final function getFormBlockType():string {return df_con_hier($this, \Df\Payment\Block\Multishipping::class);}

	/**
	 * 2016-02-11
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getInfoBlockType()
	 * How is a payment method's getInfoBlockType() used? https://mage2.pro/t/687
	 * @used-by \Magento\Payment\Helper\Data::getInfoBlock():
	 *		public function getInfoBlock(InfoInterface $info, LayoutInterface $layout = null) {
	 *			$layout = $layout ?: $this->_layout;
	 *			$blockType = $info->getMethodInstance()->getInfoBlockType();
	 *			$block = $layout->createBlock($blockType);
	 *			$block->setInfo($info);
	 *			return $block;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Payment/Helper/Data.php#L182-L196
	 * 2016-08-29 The method is called only one time, so it does not need to cache own result.
	 * 2017-01-13
	 * Задействовал @uses df_con_hier(), чтобы подхватывать @see \Df\StripeClone\Block\Info
	 * для потомков @see @see \Df\StripeClone\Method
	 * 2017-11-19 "The class Df\Payment\Block\Info should not be instantiated": https://github.com/mage2pro/core/issues/57
	 * @see \Dfe\AllPay\Method::getInfoBlockType()
	 * @see \Dfe\CheckoutCom\Method::getInfoBlockType()
	 * @see \Dfe\Moip\Method::getInfoBlockType()
	 */
	function getInfoBlockType():string {
		$r = df_con_hier($this, bInfo::class); /** @var string $r */
		/** 2017-11-19 I use @uses df_cts() to strip the «\Interceptor» suffix */
		if (bInfo::class === df_cts($r)) {
			df_error("The {$this->titleB()} module should implement a %s descendant.", bInfo::class);
		}
		return $r;
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
	 * @throws DFE|NoSuchEntityException
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
	 * @used-by dfp()
	 * @used-by \Df\Payment\TM::__construct()
	 * @used-by \Df\Payment\Facade::ii()
	 *
	 * @return II|I|OP|QP
	 */
	final function getInfoInstance() {
		if (!$this->_ii && ($q = df_quote() /** @var Q $q */)) {
			$this->setInfoInstance(dfp($q));
		}
		return $this->_ii ?: df_error('We cannot retrieve the payment information object instance.');
	}

	/**
	 * 2016-02-09
	 * @override
	 * How is a payment method's getStore() used? https://mage2.pro/t/695
	 * @see \Magento\Payment\Model\MethodInterface::getStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L49-L53
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L278-L284
	 * 2016-09-07 Для самого себя я использую метод @see store()
	 * @used-by \Df\Payment\Settings::scopeDefault()
	 */
	final function getStore():int {return $this->_storeId;}

	/**
	 * 2016-02-08
	 * @override "How is a payment method's getTitle() used?" https://mage2.pro/t/692
	 * @see \Magento\Payment\Model\MethodInterface::getTitle()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L34-L40
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getTitle()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L732-L740
	 */
	final function getTitle():string {return dfc($this, function() {return
		df_is_backend() ? $this->titleB() : $this->titleF()
	;});}

	/**
	 * 2016-03-06
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by \Df\Payment\Init\Action::token()
	 * @used-by \Df\Payment\Operation::token()
	 * @used-by \Df\Payment\Operation\Source\Order::ii()
	 * @used-by \Dfe\AlphaCommerceHub\Method::_refund()
	 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
	 * @param string|null $k [optional]
	 * @return II|I|OP|QP|mixed
	 */
	final function ii($k = null) {return dfad($this->getInfoInstance(), $k);}

	/**
	 * 2016-03-06
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by \Df\Payment\PlaceOrderInternal::setRedirectData()
	 * @param string|array(string => mixed) $k [optional]
	 * @param mixed|null $v [optional]
	 */
	final function iiaSet($k, $v = null):void {$this->ii()->setAdditionalInformation($k, $v);}

	/**
	 * 2016-09-01
	 * 2017-01-13
	 * Эта информация в настоящее время используется:
	 * 1) Для показа её на административном экране транзакции:
	 * https://site.com/admin/sales/transactions/view/txn_id/347/order_id/354/
	 * Она извлекается и обрабатывается в методе
	 * @see \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
	 * 2) Для показа её в витринном и административном блоках информации о платеже.
	 *
	 * Раньше я конвертировал массивы в JSON перед записью.
	 * Теперь я это стал делать непосредственно перед отображением: так надёжнее,
	 * потому что ранее я порой ненароком забывал сконвертировать какой-нибудь массив в JSON
	 * перед записью, и при отображении это приводило к сбою «array to string conversion».
	 *
	 * @used-by \Df\GingerPaymentsBase\Init\Action::req()
	 * @used-by \Df\GingerPaymentsBase\Init\Action::res()
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by \Df\StripeClone\Method::transInfo()
	 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
	 * @used-by \Dfe\Qiwi\Init\Action::preorder()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
	 *
	 * @param string|array(string => mixed)|null $req
	 * @param string|array(string => mixed)|null $res
	 */
	final function iiaSetTRR($req, $res = null):void {$i = $this->ii(); df_trd_set($i,
		df_clean([self::IIA_TR_REQUEST => $req, self::IIA_TR_RESPONSE => $res])
		+ dfa($i->getTransactionAdditionalInfo(), T::RAW_DETAILS, [])
	);}

	/**
	 * 2016-02-15 How is a payment method's initialize() used? https://mage2.pro/t/722
	 * @see \Magento\Payment\Model\MethodInterface::initialize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L361-L372
	 * @see \Magento\Payment\Model\Method\AbstractMethod::initialize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L838-L852
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::initialize()
	 * @param string $paymentAction
	 * @param object $stateObject
	 * 2017-02-08
	 * @see \Df\StripeClone\Method::initialize()
	 */
	function initialize($paymentAction, $stateObject):void {}

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
	 * Но раз уж этот метод присутствует в интерфейсе, то я его использую в методе @used-by self::s()
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
	 */
	final function isActive($storeId = null):bool {return $this->s()->b('enable', $storeId);}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's isAvailable() used? https://mage2.pro/t/721
	 * @see \Magento\Payment\Model\MethodInterface::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L343-L350
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L805-L825
	 * @used-by \Magento\Payment\Block\Form\Container::getMethods()
	 * @used-by \Magento\Payment\Helper\Data::getStoreMethods()
	 * @used-by \Magento\Payment\Model\MethodList::getAvailableMethods()
	 * @used-by \Magento\Quote\Model\Quote\Payment::importData()
	 * @used-by \Magento\Sales\Model\AdminOrder\Create::_validate()
	 * @param ICart|Q $q [optional]
	 */
	final function isAvailable(ICart $q = null):bool {
		if ($q) {
			# 2019-05-10
			# It fixes the issue: "«Call to a member function getStoreId() on null» on a backend ordering":
			# https://github.com/mage2pro/core/issues/85
			$qp = $q->getPayment(); /** @var QP $qp */
			if (!$qp->getQuote()) {
				$qp->setQuote($q);
			}
			/**
			 * 2019-01-17
			 * It fixes the issue:
			 * «Call to a member function getStore() on null in vendor/mage2pro/core/Payment/Currency.php:69»
			 * https://github.com/mage2pro/core/issues/84
			 * The issue was occured because this method checks payment limits (see the code below),
			 * and such checkings require the current quote or order: @see \Df\Payment\Currency::oq()
			 * So the info instance should be already initialized for such checkings.
			 */
			$this->setInfoInstance($qp);
		}
		if ($r = ($this->availableInBackend() || !df_is_backend()) && $this->isActive($q ? $q->getStoreId() : null)) {
			df_dispatch('payment_method_is_active', ['method_instance' => $this, 'quote' => $q,
				'result' => ($evR = new _DO(['is_available' => true])) /** @var _DO $evR */
			]);
			$r = $evR['is_available'];
		} /** @var bool $r */
		# 2017-02-08
		# Допустимы следующие форматы $limits:
		# 1) null или [] — отсутствие лимитов.
		# 2) [min, max] — общие лимиты для всех валют
		# 3) \Closure — лимиты вычисляются динамически для конкретной валюты
		# 4) ['USD' => [min, max], '*' => [min, max]] — лимиты заданы с таблицей,
		# причём '*' — это лимиты по умолчанию.
		/** @var null|[]|\Closure|array(int|float|null)|array(string => array(int|float|null)) $limits */
		if ($r && $q && ($limits = $this->amountLimits())) {
			/**
			 * 2017-10-12
			 * M2 core does the following:
			 *	if (!$method->isAvailable($quote) || !$methodSpecification->isApplicable($method, $quote)) {
			 *		throw new \Magento\Framework\Exception\LocalizedException(
			 *		__('The requested Payment Method is not available.')
			 *		);
			 *	}
			 *	$method->assignData($data);
			 * So it calls isAvailable() first (it is the current method), and then call assignData().
			 * But the Stripe's currency code depends on $data:
			 * @see \Dfe\Stripe\Method::cardType()
			 * It checks `additional_data` for the chosen bank card type.
			 * So we need to call assignData() first.
			 * Moreover, it the previous payment attemt was failed,
			 * then @see iia() contains outdated data.
			 *
			 * @var _DO|null $data
			 * @data is null, if we get here not from @see \Magento\Quote\Model\Quote\Payment::importData()
			 */
			if ($data = \Df\Quote\Observer\Payment\ImportDataBefore::data()) {
				$this->assignData($data);
			}
			$c = $this->currency(); /** @var Currency $c */
			$a = $c->fromBase($q->getBaseGrandTotal(), $q); /** @var float $a */
			$cc = $c->oq($q); /** @var string $cc */
			/** @var null|array(int|float|null) $limitsForCurrency */
			if ($limitsForCurrency = $limits instanceof \Closure ? $limits($cc) : (
				array_is_list($limits) ? $limits : dfa($limits, $cc, dfa($limits, '*'))
			)) {
				# 2020-03-02, 2022-10-31
				# 1) Symmetric array destructuring requires PHP ≥ 7.1:
				#		[$a, $b] = [1, 2];
				# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
				# We should support PHP 7.0.
				# https://3v4l.org/3O92j
				# https://www.php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
				# https://stackoverflow.com/a/28233499
				list($min, $max) = $limitsForCurrency; /** @var int|float|null $min */ /** @var int|float|null $max */
				$r = (is_null($min) || $a >= $min) && (is_null($max) || $a <= $max);
			}
		}
		return $r;
	}

	/**
	 * 2016-02-11
	 * Насколько я понял, isGateway должно возвращать true,
	 * если процесс оплаты должен происходить непосредственно на странице оформления заказа,
	 * без перенаправления на страницу платёжной системы.
	 * В Российской сборке Magento так пока работает только метод @see Df_Chronopay_Model_Gate,
	 * однако он изготовлен давно и по устаревшей технологии,
	 * и поэтому не является наследником класса @see Df_Payment_Model_Method
	 * How is a payment method's isGateway() used? https://mage2.pro/t/679
	 * 2017-11-11
	 * *) The method should return `true`, if it uses PSP API calls.
	 * The @see \Df\StripeClone\Method descendants behave is such way.
	 * *) The method should return `false`, if it does not use direct PSP API calls,
	 * but implement its payments interactively instead: a customer is redirected to a PSP payment page.
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::isGateway()
	 *
	 * 1) @used-by \Magento\Sales\Block\Adminhtml\Order\View::_construct():
	 *		$message = __(
	 *			'This will create an offline refund. ' .
	 *			'To create an online refund, open an invoice and create credit memo for it. ' .
	 * 			'Do you want to continue?'
	 *		);
	 *		$onClick = "setLocation('{$this->getCreditmemoUrl()}')";
	 *		if ($order->getPayment()->getMethodInstance()->isGateway()) {
	 *			$onClick = "confirmSetLocation('{$message}', '{$this->getCreditmemoUrl()}')";
	 *		}
	 *		$this->addButton(
	 *			'order_creditmemo',
	 *			['label' => __('Credit Memo'), 'onclick' => $onClick, 'class' => 'credit-memo']
	 *		);
	 * The code is the same in Magento 2.0.0 - 2.2.1:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Order/View.php#L135-L146
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Block/Adminhtml/Order/View.php#L137-L148
	 *
	 * 2) @used-by \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items::isGatewayUsed():
	 *		public function isGatewayUsed() {
	 *			return $this->getInvoice()->getOrder()->getPayment()->getMethodInstance()->isGateway();
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Order/Invoice/Create/Items.php#L239-L247
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Block/Adminhtml/Order/Invoice/Create/Items.php#L242-L250
	 * It is then used in the Magento/Sales/view/adminhtml/templates/order/invoice/create/items.phtml template
	 * to warn a backend user if the `capture` operations is not available:
	 * 		«The invoice will be created offline without the payment gateway»
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/view/adminhtml/templates/order/invoice/create/items.phtml#L93-L113
	 *
	 * 3) @used-by \Magento\Sales\Model\Order\Invoice::register():
     *   $captureCase = $this->getRequestedCaptureCase();
	 *		if ($this->canCapture()) {
	 *			if ($captureCase) {
	 *				if ($captureCase == self::CAPTURE_ONLINE) {
	 *					$this->capture();
	 *				}
	 *				elseif ($captureCase == self::CAPTURE_OFFLINE) {
	 *					$this->setCanVoidFlag(false);
	 *					$this->pay();
	 *				}
	 *			}
	 *		}
	 *		elseif (
	 *			!$order->getPayment()->getMethodInstance()->isGateway()
	 *			|| $captureCase == self::CAPTURE_OFFLINE
	 *		) {
	 *			if (!$order->getPayment()->getIsTransactionPending()) {
	 *				$this->setCanVoidFlag(false);
	 *				$this->pay();
	 *			}
	 *		}
	 * The code is the same in Magento 2.0.0 - 2.2.1:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Invoice.php#L599-L614
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Invoice.php#L611-L626
	 * In this scenario isGateway() is important
	 * to avoid the @see \Magento\Sales\Model\Order\Invoice::pay() call
	 * (which marks order as paid without any actual PSP API calls).
	 * @see \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 *
	 * 4) @used-by \Magento\Sales\Model\Order\Invoice\PayOperation::execute():
	 *		if ($this->canCapture($order, $invoice)) {
	 *			if ($capture) {
	 *				$invoice->capture();
	 *			}
	 *			else {
	 *				$invoice->setCanVoidFlag(false);
	 *				$invoice->pay();
	 *			}
	 *		}
	 *		elseif (!$order->getPayment()->getMethodInstance()->isGateway() || !$capture) {
	 *			if (!$order->getPayment()->getIsTransactionPending()) {
	 *				$invoice->setCanVoidFlag(false);
	 *				$invoice->pay();
	 *			}
	 *		}
	 * It was introduced in Magento 2.1.2.
	 * The code is the same in Magento 2.1.2 - 2.2.1:
	 * https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Sales/Model/Order/Invoice/PayOperation.php#L43-L57
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Invoice/PayOperation.php#L43-L57
	 * @see \Df\StripeClone\Method::isGateway()
	 */
	function isGateway():bool {return false;}

	/**
	 * 2016-02-11 How is a payment method's isInitializeNeeded() used? https://mage2.pro/t/681
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::isInitializeNeeded()
	 * @used-by \Magento\Sales\Model\Order\Payment::place():
	 *		if ($action) {
	 *			if ($methodInstance->isInitializeNeeded()) {
	 *				$stateObject = new \Magento\Framework\DataObject();
	 *				# For method initialization we have to use original config value for payment action
	 *				$methodInstance->initialize($methodInstance->getConfigData('payment_action'), $stateObject);
	 *				$orderState = $stateObject->getData('state') ?: $orderState;
	 *				$orderStatus = $stateObject->getData('status') ?: $orderStatus;
	 *				$isCustomerNotified = $stateObject->hasData('is_notified')
	 *					? $stateObject->getData('is_notified')
	 *					: $isCustomerNotified;
	 *			}
	 *			else {
	 * The code is the same in Magento 2.0.0 - 2.2.1:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L324-L334
	 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment.php#L356-L366
	 * @see \Df\StripeClone\Method::isInitializeNeeded()
	 */
	function isInitializeNeeded():bool {return false;}

	/**
	 * 2016-02-11
	 * @override
	 * "How is a payment method's isOffline() used?" https://mage2.pro/t/680
	 * @see \Magento\Payment\Model\MethodInterface::isOffline()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L168-L174
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isOffline()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L442-L451
	 */
	final function isOffline():bool {return false;}

	/**
	 * 2016-03-15
	 * @used-by \Dfe\CheckoutCom\Method::need3DS()
	 * @used-by \Dfe\Stripe\Init\Action::need3DS()
	 * @used-by \Dfe\TwoCheckout\Method::charge()
	 * @throws InputException|LE|NoSuchEntityException
	 */
	final function o():O {return df_order($this->ii());}

	/**
	 * 2017-02-07
	 * 2017-10-26
	 * A customer has reported that this method can return `null`, but I am unable to reproduce it:
	 * https://mage2.pro/t/4764
	 * 2018-10-07 We should not cache the result: https://github.com/mage2pro/core/issues/80
	 * @used-by dfp_due()
	 * @used-by self::convert()
	 * @used-by self::cPayment()
	 * @used-by \Df\Payment\Init\Action::oq()
	 * @used-by \Df\Payment\Operation::__construct()
	 * @return O|Q
	 */
	final function oq() {return $this->ii()->getOrder() ?: $this->ii()->getQuote();}

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
	 * @param float $a
	 */
	final function order(II $payment, $a):void {df_should_not_be_here();}

	/**
	 * 2018-04-14
	 * @see \Dfe\Tap\Model\Tap::orderPlaced()
	 * @used-by \Df\Payment\PlaceOrderInternal::_place()
	 * @param int $id
	 */
	function orderPlaced($id):void {}

	/**
	 * 2016-02-15 How is a payment method's refund() used? https://mage2.pro/t/709
	 * 2017-04-12
	 * Заметил, что в магазине pumpunderwear.com
	 * сюда каким-то макаром в качестве $a попало значение «0».
	 * https://sentry.io/dmitry-fedyuk/mage2pro/issues/251676347/
	 * Это привело к сбою в модуле Stripe:
	 *	"error": {
	 *		"type": "invalid_request_error",
	 *		"message": "Invalid positive integer",
	 *		"param": "amount"
	 *	}
	 * Пока решил с этим ничего не делать, потому что сталкиваюсь с этим впервые, и причины пока неясны.
	 * 2017-04-13 Я у себя этот сбой воспроизвести не смог.
	 * 2021-07-02
	 * $a is a string because it is a result of the @see \Magento\Sales\Model\Order\Payment::formatAmount() call:
	 * 		$baseAmountToRefund = $this->formatAmount($creditmemo->getBaseGrandTotal());
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment.php#L655
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L269-L277
	 * @see \Magento\Payment\Model\Method\AbstractMethod::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L640-L656
	 * @used-by \Magento\Sales\Model\Order\Payment::refund()
	 * 		$gateway->refund($this, $baseAmountToRefund);
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment.php#L684
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment.php#L701
	 * @param II|I|OP $payment
	 * @param string|float $a
	 */
	final function refund(II $payment, $a):self {
		df_cm_set_increment_id($this->ii()->getCreditmemo());
		$this->action(function() use($a) {$this->_refund($this->cFromBase($a));});
		return $this;
	}

	/**
	 * 2017-12-13
	 * "Provide an ability to the Magento backend users (merchants)
	 * to set up the «Require the billing address?» option separately
	 * for each AlphaCommerceHub's payment option (bank cards, PayPal, POLi Payments, etc.)":
	 * https://github.com/mage2pro/alphacommercehub/issues/84
	 * @used-by \Df\Payment\PlaceOrderInternal::_place()
	 * @see \Dfe\AlphaCommerceHub\Method::requireBillingAddress()
	 */
	function requireBillingAddress():bool {return $this->s()->requireBillingAddress();}

	/**
	 * 2016-07-13
	 * 2017-07-02
	 * Сегодня заметил, что параметр scope сюда никто не передаёт, поэтому убрал его.
	 * @see \Df\Payment\Settings::scopeDefault()
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by dfps()
	 * @used-by self::action()
	 * @used-by self::canUseForCountryP()
	 * @used-by self::cardTypes()
	 * @used-by self::getConfigData()
	 * @used-by self::isActive()
	 * @used-by self::requireBillingAddress()
	 * @used-by self::test()
	 * @used-by self::titleB()
	 * @used-by self::titleF()
	 * @used-by \Df\Payment\Block\Info::s()
	 * @used-by \Df\Payment\Currency::s()
	 * @used-by \Df\Payment\Facade::ss()
	 * @used-by \Df\Payment\Init\Action::s()
	 * @used-by \Df\Payment\W\Strategy::s()
	 * @used-by \Dfe\AlphaCommerceHub\Method::optionTitle()
	 * @used-by \Dfe\AlphaCommerceHub\Method::urlBase()
	 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
	 * @param string|null $k [optional]
	 * @param mixed|callable $d [optional]
	 * @return Settings|mixed
	 */
	function s($k = null, $d = null) {
		$r = dfc($this, function() { /** @var Settings $r */
			if (!($c = df_con_hier($this, Settings::class, false))) { /** @var string $c */
				df_error('Unable to find a proper «Settings» class for the «%s» payment module.',
					df_module_name($this)
				);
			}
			return new $c($this);
		});
		return is_null($k) ? $r : $r->v($k, null, $d);
	}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's setInfoInstance() used? https://mage2.pro/t/697
	 * 2017-03-30
	 * Каждый потомок Method является объектом-одиночкой: @see _s(),
	 * но вот info instance в него может устанавливаться разный: @see setInfoInstance()
	 * Так происходит, например, в методе @see \Df\Payment\Observer\DataProvider\SearchResult::execute()
	 * https://github.com/mage2pro/core/blob/2.4.13/Payment/Observer/DataProvider/SearchResult.php#L52-L65
	 * Аналогично, в Method может устанавливаться разный store: @see setStore()
	 * Поэтому будьте осторожны с кэшированием внутри Method!
	 * @used-by self::getInfoInstance()
	 * @used-by self::isAvailable()
	 * @param II|I|OP|QP $i
	 */
	final function setInfoInstance(II $i):void {
		$this->_ii = $i;
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
		if ($i instanceof OP) {
			$this->setStore($i->getOrder()->getStoreId());
		}
		/**
		 * 2019-01-17
		 * Fron now on, we can get here from @see isAvailable(), and in this case we need to set the store manually too.
		 */
		elseif ($i instanceof QP) {
			$this->setStore($i->getQuote()->getStoreId());
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
	 * @used-by self::setInfoInstance()
	 * @param int $storeId
	 */
	final function setStore($storeId):void {$this->_storeId = (int)$storeId;}

	/**
	 * 2018-04-15
	 * @see \Dfe\Tap\Model\Tap::skipDfwEncode()
	 * @used-by \Df\Payment\PlaceOrderInternal::_place()
	 */
	function skipDfwEncode():bool {return false;}

	/**
	 * 2016-09-07 Намеренно не используем @see _storeId
	 * @used-by \Dfe\Robokassa\Choice::title()
	 */
	final function store():Store {return dfc($this, function() {return $this->o()->getStore();});}

	/**
	 * 2017-01-22 Первый аргумент — для тестового режима, второй — для промышленного.
	 * @used-by dfp_sentry_tags()
	 * @used-by self::validate()
	 * @used-by \Df\Payment\Url::url()
	 * @used-by \Dfe\SecurePay\Method::amountFormat()
	 * @param mixed ...$a [optional]
	 * @return bool|mixed
	 */
	final function test(...$a) {return df_b($a, $this->s()->test());}

	/**
	 * 2017-08-30
	 * @override
	 * @see \Df\Core\ICached::tags()
	 * @used-by \Df\Core\RAM::set()
	 * @return string[]
	 */
	final function tags():array {return [self::$CACHE_TAG];}

	/**
	 * 2017-03-22
	 * @used-by self::tidFormat()
	 * @used-by \Df\Payment\Init\Action::e2i()
	 * @used-by \Df\PaypalClone\W\Nav::e2i()
	 * @used-by \Df\StripeClone\Method::e2i()
	 * @used-by \Df\StripeClone\Method::i2e()
	 * @used-by \Df\StripeClone\W\Nav::e2i()
	 * @used-by \Dfe\AlphaCommerceHub\Method::_refund()
	 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 */
	final function tid():TID {return TID::s($this);}

	/**
	 * 2016-08-20
	 * @used-by \Df\Payment\Block\Info::siID()
	 * @used-by \Df\Payment\Observer\FormatTransactionId::execute()
	 * @param T $t
	 * @param bool $e [optional]
	 */
	final function tidFormat(T $t, $e = false):string {
		$id = $t->getTxnId(); /** @var string $id */
		return df_tag_if(!$e ? $id : $this->tid()->i2e($id), $url = $this->transUrl($t), 'a', [/** @var string|null $url */
			'href' => $url, 'target' => '_blank', 'title' => __(
				'View the transaction in the %1 interface', $this->getTitle()
			)
		]);
	}

	/**
	 * 2017-01-13
	 * @used-by dfpm_title()
	 * @used-by self::action()
	 * @used-by self::getInfoBlockType()
	 * @used-by self::getTitle()
	 * @used-by \Df\GingerPaymentsBase\Charge::pClient()
	 * @used-by \Df\Payment\Block\Info::titleB()
	 * @used-by \Df\Payment\ConfigProvider::config()
	 */
	final function titleB():string {return $this->s('title_backend', function() {return df_class_second($this);});}

	/**
	 * 2016-02-12 How is a payment method's validate() used? https://mage2.pro/t/698
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L230-L237
	 * @see \Magento\Payment\Model\Method\AbstractMethod::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L566-L583
	 * @used-by \Magento\Quote\Model\Quote\Payment::importData()
	 * 		$method->validate();
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Quote/Model/Quote/Payment.php#L202
	 * @used-by \Magento\Sales\Model\AdminOrder\Create::_validate()
	 * 		$method->validate();
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/AdminOrder/Create.php#L2012
	 * @used-by \Magento\Sales\Model\Order\Payment::place()
	 * 		$methodInstance->validate();
	 * @throws LE
	 */
	final function validate():self {
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
	 * 2016-02-15 How is a payment method's void() used? https://mage2.pro/t/712
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L288-L295
	 * @see \Magento\Payment\Model\Method\AbstractMethod::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L671-L686
	 * @param II|I|OP $payment
	 * @uses self::_void()
	 */
	final function void(II $payment):self {
		$this->action('_void');
		/**
		 * 2017-01-17
		 * В @see \Df\Payment\Observer\VoidT мы закрываем заказ,
		 * и там объяснено, почему мы не можем этого делать здесь.
		 */
		return $this;
	}

	/**
	 * 2016-08-14
	 * @used-by self::_void()
	 * @used-by self::refund()
	 * @see \Df\StripeClone\Method::_refund()
	 * @see \Dfe\AlphaCommerceHub\Method::_refund()
	 * @see \Dfe\CheckoutCom\Method::_refund()
	 * @see \Dfe\SecurePay\Method::_refund()
	 * @see \Dfe\TwoCheckout\Method::_refund()
	 */
	protected function _refund(float $a):void {}

	/**
	 * 2016-08-14
	 * @used-by self::void()
	 * @see \Dfe\CheckoutCom\Method::_void()
	 */
	protected function _void():void {$this->_refund($this->cFromBase($this->ii()->getBaseAmountAuthorized()));}

	/**
	 * 2016-11-13
	 * @used-by self::amountFormat()
	 * @used-by self::amountParse()
	 * @see \Dfe\AllPay\Method::amountFactor()
	 * @see \Dfe\AlphaCommerceHub\Method::amountFactor()
	 * @see \Dfe\Dragonpay\Method::amountFactor()
	 * @see \Dfe\IPay88\Method::amountFactor()
	 * @see \Dfe\Robokassa\Method::amountFactor()
	 * @see \Dfe\TwoCheckout\Method::amountFactor()
	 */
	protected function amountFactor():int {return df_find(function($factor, $list) {return
		in_array($this->cPayment(), df_csv_parse($list)) ? $factor : null
	;}, $this->amountFactorTable(), [], [], DF_BEFORE) ?: 100;}

	/**
	 * 2016-11-13
	 * @used-by self::amountFactor()
	 * @see \Dfe\CheckoutCom\Method::amountFactorTable()
	 * @see \Dfe\Stripe\Method::amountFactorTable()
	 * @return array(int => string|string[])
	 */
	protected function amountFactorTable():array {return [];}

	/**
	 * 2016-02-29 Решил, что значением по умолчанию разумно сделать false.
	 * @used-by self::isAvailable()
	 */
	final protected function availableInBackend():bool {return false;}

	/**
	 * 2016-03-08
	 * @used-by self::getConfigData()
	 */
	final protected function cardTypes():string {return $this->s('cctypes');}

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
	 * а в обработчике оповещений от платёжной системы: @see \Df\Payment\W\Handler
	 *
	 * @used-by self::authorize()
	 * @used-by self::capture()
	 * @see \Df\StripeClone\Method::charge()
	 * @see \Dfe\AlphaCommerceHub\Method::charge()
	 * @see \Dfe\CheckoutCom\Method::charge()
	 * @see \Dfe\TwoCheckout\Method::charge()
	 */
	protected function charge(bool $capture = true):void {}

	/**
	 * 2016-12-28
	 * 2017-01-10
	 * Назначение этого метода — конвертация исключительных ситуаций
	 * библиотеки платёжной системы в наши.
	 * Исключительные ситуации библиотеки платёжной системы имеют свою внутреннуюю структуру,
	 * да и их диагностические сообщения — это не всегда то, что нам нужно.
	 * По этой причине мы их конвертируем в свои.
	 * Пока данная функциональность используется модулем Stripe.
	 * @used-by self::action()
	 * @see \Dfe\Stripe\Method::convertException()
	 * @param E $e
	 */
	protected function convertException(E $e):E {return $e;}

	/**
	 * 2016-03-06
	 * @used-by \Df\GingerPaymentsBase\Method::bank()
	 * @used-by \Df\GingerPaymentsBase\Method::option()
	 * @used-by \Dfe\AllPay\Method::option()
	 * @used-by \Dfe\Moip\Method::taxID()
	 * @used-by \Dfe\Qiwi\Method::phone()
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @used-by \Dfe\TwoCheckout\Method::_refund()
	 * @used-by \Dfe\Vantiv\Method::card()
	 * @param string ...$k
	 * @return mixed|array(string => mixed)
	 */
	final protected function iia(...$k) {return dfp_iia($this->ii(), ...$k);}

	/**
	 * 2016-07-10
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @used-by \Dfe\TwoCheckout\Method::charge()
	 * @param array(string => mixed) $v
	 */
	final protected function iiaAdd(array $v):array {dfp_add_info($this->ii(), $v);}

	/**
	 * 2016-05-03
	 * @used-by self::assignData()
	 * @see \Df\GingerPaymentsBase\Method::iiaKeys()
	 * @see \Df\StripeClone\Method::iiaKeys()
	 * @see \Dfe\ACH\Method::iiaKeys()
	 * @see \Dfe\AllPay\Method::iiaKeys()
	 * @see \Dfe\AlphaCommerceHub\Method::iiaKeys()
	 * @see \Dfe\CheckoutCom\Method::iiaKeys()
	 * @see \Dfe\IPay88\Method::iiaKeys()
	 * @see \Dfe\Qiwi\Method::iiaKeys()
	 * @see \Dfe\Square\Method::iiaKeys()
	 * @see \Dfe\TBCBank\Method::iiaKeys()
	 * @see \Dfe\TwoCheckout\Method::iiaKeys()
	 * @see \Dfe\Vantiv\Method::iiaKeys()
	 * @see \Dfe\YandexKassa\Method::iiaKeys()
	 * @return string[]
	 */
	protected function iiaKeys():array {return [];}

	/**
	 * 2016-08-14
	 * @used-by \Dfe\TwoCheckout\Method::charge()
	 * @param string|array(string => mixed) $k [optional]
	 * @param mixed|null $v [optional]
	 */
	final protected function iiaUnset($k, $v = null):void {$this->ii()->unsAdditionalInformation($k, $v);}

	/**
	 * 2017-02-01
	 * До сегодняшнего дня Stripe-подобные модули для каждой платёжной операции
	 * создавали как минимум (не считая webhooks) 3 записи в логах:
	 * 1) Stripe: getConfigPaymentAction
	 * 2) [Stripe] chargeNew
	 * 3) Stripe: capture
	 * №1 и №3 создавались как из @used-by self::action()
	 * Нам не нужно так много записей для единственной операции,
	 * поэтому добавил сейчас возможность отключать логирование в action().
	 * @used-by self::action()
	 * @see \Df\StripeClone\Method::needLogActions()
	 */
	protected function needLogActions():bool {return true;}

	/**
	 * 2016-09-06
	 * @used-by \Dfe\TwoCheckout\Method::charge()
	 */
	final protected function oii():string {return $this->o()->getIncrementId();}

	/**
	 * 2017-08-02
	 * @used-by self::getTitle()
	 * @used-by \Dfe\Moip\Method::titleF()
	 * @see \Dfe\Moip\Method::titleF()
	 */
	protected function titleF():string {return $this->s('title', null, function() {return df_class_second($this);});}

	/**
	 * 2016-08-20
	 * @used-by self::tidFormat()
	 * @see \Df\GingerPaymentsBase\Method::transUrl()
	 * @see \Df\StripeClone\Method::transUrl()
	 * @param T $t
	 * @return string|null
	 */
	protected function transUrl(T $t) {return null;}

	/**
	 * 2017-03-30 Цель этого метода — запретить использовать для класса оператор new вне класса.
	 * @used-by self::_s()
	 */
	private function __construct() {}

	/**
	 * 2016-09-06
	 * @used-by self::cFromBase()
	 * @used-by self::cToBase()
	 * @used-by self::cToOrder()
	 * @uses \Df\Payment\Currency::fromBase()
	 * @uses \Df\Payment\Currency::fromOrder()
	 * @uses \Df\Payment\Currency::toBase()
	 * @uses \Df\Payment\Currency::toOrder()
	 * @param float $a
	 */
	private function convert($a):float {return call_user_func(
		[$this->currency(), lcfirst(substr(df_caller_f(), 1))], $a, $this->oq()
	);}

	/**
	 * 2017-10-12
	 * @used-by self::convert()
	 * @used-by self::cPayment()
	 * @used-by self::isAvailable()
	 */
	private function currency():Currency {return dfc($this, function() {return dfp_currency($this);});}

	/**
	 * 2016-02-12
	 * @used-by self::getInfoInstance()
	 * @used-by self::setInfoInstance()
	 * @var II|I|OP|QP
	 */
	private $_ii;

	/**
	 * 2016-02-09
	 * @used-by self::getStore()
	 * @used-by self::setStore()
	 * @var int
	 */
	private $_storeId;

	/**
	 * 2016-07-13
	 * @used-by dfp_is_test()
	 * @used-by self::validate()
	 */
	const II__TEST = 'df_test';

	/**
	 * 2017-01-19
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 * @used-by \Df\StripeClone\Method::_refund()
	 */
	const II_TRANS = 'df_sc_transactions';

	/**
	 * 2017-03-22
	 * @used-by self::iiaSetTRR()
	 * @used-by \Df\Payment\TM::req()
	 */
	const IIA_TR_REQUEST = 'Request';

	/**
	 * 2016-12-29
	 * @used-by self::iiaSetTRR()
	 * @used-by \Df\GingerPaymentsBase\Method::transUrl()
	 * @used-by \Df\Payment\TM::res0()
	 * @used-by \Dfe\Stripe\Block\Info::cardData()
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
	 * @used-by dfp_webhook_case()
	 * @used-by self::action()
	 */
	const WEBHOOK_CASE = 'df_webhook_case';

	/**
	 * 2016-07-10
	 * @used-by dfpm_code()
	 * @used-by self::getCode()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @uses \Dfe\CheckoutCom\Method::CODE
	 * @uses \Dfe\IPay88\Method::CODE
	 * @uses \Dfe\TwoCheckout\Method::CODE
	 */
	final static function codeS():string {return dfcf(function($c) {return df_const(
		$c, 'CODE', function() use($c) {return df_module_name_lc($c);}
	);}, [static::class]);}

	/**
	 * 2017-03-30
	 * Замечание №1.
	 * При текущей реализации мы осознанно не поддерживаем interceptors, потому что:
	 * 1) Похоже, что невозможно определить, имеется ли для некоторого класса interceptor,
	 * потому что вызов @uses class_exists(interceptor) приводит к созданию interceptor'а
	 * (как минимум — в developer mode), даже если его раньше не было.
	 * 2) У нас потомки Method объявлены как final.
	 *
	 * Замечание №2.
	 * Каждый потомок Method является объектом-одиночкой: @see \Df\Payment\Method::sg(),
	 * но вот info instance в него может устанавливаться разный: @see \Df\Payment\Method::setInfoInstance()
	 * Так происходит, например, в методе @see \Df\Payment\Observer\DataProvider\SearchResult::execute()
	 * https://github.com/mage2pro/core/blob/2.4.13/Payment/Observer/DataProvider/SearchResult.php#L52-L65
	 * Аналогично, в Method может устанавливаться разный store: @see \Df\Payment\Method::setStore()
	 * Поэтому будьте осторожны с кэшированием внутри Method!
	 *
	 * @used-by dfpm()
	 * @used-by \Df\Payment\Plugin\Model\Method\FactoryT::aroundCreate()
	 * @param string $c
	 */
	final static function sg($c):self {return dfcf(function($c) {return new $c;}, [dfpm_c($c)]);}

	/**
	 * 2017-08-28
	 * @used-by \Df\Payment\Observer\Multishipping::execute()
	 */
	final static function sgReset():void {df_ram()->clean(self::$CACHE_TAG);}

	/**
	 * 2017-08-28
	 * @used-by self::sgReset()
	 * @used-by self::tags()
	 * @var string
	 */
	private static $CACHE_TAG = __CLASS__;
}