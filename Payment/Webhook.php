<?php
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Df\Framework\Controller\Result\Text;
use Df\Payment\Settings as S;
use Df\Sales\Model\Order as DfOrder;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
// 2016-07-09
// Портировал из Российской сборки Magento.
abstract class Webhook extends \Df\Core\O {
	/**
	 * 2017-01-01
	 * @used-by handle()
	 * @return void
	 */
	abstract protected function _handle();

	/**
	 * 2016-08-27
	 * 2016-12-31
	 * Перекрытие этого метода позволяет потомкам разом задать набор параметров данного класса.
	 * Такая техника является более лаконичным вариантом,
	 * нежели объявление и перекрытие методов для отдельных параметров.
	 * @used-by configCached()
	 * @return array(string => mixed)
	 */
	abstract protected function config();

	/**
	 * Webhook constructor.
	 * @param array(string => mixed) $req
	 * @param array(string => mixed) $extra [optional]
	 */
	public function __construct(array $req, array $extra = []) {
		parent::__construct();
		$this->_extra = $extra;
		$this->_req = $req;
		/**
		 * 2017-01-02
		 * Раньше я выполнял это доинициалиацию req непосредственно в методе req(),
		 * но это было не совсем правильно, потому что метод req() вызывал метод test(),
		 * а метод test() — обратно метод req().
		 * К бесконечной рекурсии это, к счастью, не приводило,
		 * но приводило к образованию нескольких дубликатов кэша у req(),
		 * да и вообще это неправильно.
		 * Поэтому теперь делаю доинициализацию именно в конструкторе.
		 */
		if ($this->test()) {
			$this->_req += $this->testData();
		}
	}

	/**
	 * 2016-07-10
	 * 2016-12-31
	 * Возвращает идентификатор текущего платежа в платёжной системе.
	 * Этот идентификатор мы используем двояко:
	 * 1) Для последующих запросов к платёжной системе.
	 * 2) Для отображения администратору магазина
	 * (при возможности — с прямой ссылкой на страницу платежа
	 * в личном кабинете магазина в платёжной системе)
	 * @used-by \Dfe\AllPay\Block\Info::_prepareSpecificInformation()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 * @used-by id()
	 * @return string
	 */
	final public function externalId() {return $this->cv(self::$externalIdKey);}

	/**
	 * 2016-07-04
	 * @override
	 * @return Result
	 */
	final public function handle() {
		/** @var Result $result */
		try {
			if ($this->ss()->log()) {
				$this->log();
			}
			$this->validate();
			$this->addTransaction();
			$this->_handle();
			$result = $this->resultSuccess();
		}
		catch (\Exception $e) {
			$this->log($e);
			/**
			 * 2016-07-15
			 * Раньше тут стояло
					if ($this->_order) {
						$this->_order->cancel();
						$this->_order->save();
					}
			 * На самом деле, исключительная ситуация свидетельствует о сбое в программе,
			 * либо о некорректном запросе якобы от платёжного сервера (хакерской попытке, например),
			 * поэтому отменять заказ тут неразумно.
			 * В случае сбоя платёжная система будет присылать
			 * повторные оповещения — вот пусть и присылает,
			 * авось мы к тому времени уже починим программу, если поломка была на нашей строне
			 */
			$result = static::resultError($e);
		}
		return $result;
	}

	/**
	 * 2016-07-09
	 * @used-by parentIdG()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @return string
	 */
	final public function parentId() {return $this->req($this->parentIdKey());}

	/**
	 * 2016-07-10
	 * @used-by \Dfe\SecurePay\Signer\Response::req()
	 * @param string|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	final public function parentInfo($k = null) {return dfak($this, function() {return
		df_trans_raw_details($this->tParent())
	;}, $k);}

	/**
	 * 2017-01-01
	 * @used-by \Dfe\AllPay\Block\Info\ATM::paymentId()
	 * @used-by \Dfe\AllPay\Webhook\BankCard::isInstallment()
	 * @used-by \Dfe\AllPay\Webhook\Offline::expiration()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final public function req($k = null, $d = null) {return dfak($this->_req, $k, $d);}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by handle()
	 * @return void
	 * @throws \Exception
	 */
	public function validate() {}

	/**
	 * 2016-07-12
	 * @return void
	 */
	final protected function addTransaction() {
		/**
		 * 2016-08-29
		 * Идентификатор транзакции мы предварительно установили в методе @see ii()
		 */
		$this->m()->applyCustomTransId();
		dfp_set_transaction_info($this->ii(), $this->req());
		/**
		 * 2016-07-12
		 * @used-by \Magento\Sales\Model\Order\Payment\Transaction\Builder::linkWithParentTransaction()
		 */
		$this->ii()->setParentTransactionId($this->tParent()->getTxnId());
		/**
		 * 2016-07-10
		 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
		 * это единственная транзакции без специального назначения,
		 * и поэтому мы можем безопасно его использовать.
		 *
		 * 2017-01-01
		 * @uses \Magento\Sales\Model\Order\Payment::addTransaction()
		 * создаёт и настраивает объект-транзакцию, но не записывает её в базу данных,
		 * поэтому если мы далее осуществляем операцию @see capture(),
		 * то там будет использована эта же транзакция, только её тип обновится на
		 * @see \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE
		 * @see \Magento\Sales\Model\Order\Payment\Transaction\Manager::generateTransactionId():
				if (!$payment->getParentTransactionId()
					&& !$payment->getTransactionId() && $transactionBasedOn
		 		) {
					$payment->setParentTransactionId($transactionBasedOn->getTxnId());
				}
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment/Transaction/Manager.php#L73-L75
		 */
		$this->ii()->addTransaction(T::TYPE_PAYMENT);
	}

	/**
	 * 2016-08-27
	 * @param string $k
	 * @param string|null $d [optional]
	 * @param bool $required [optional]
	 * @return mixed
	 */
	final protected function cv($k = null, $d = null, $required = true) {
		$k = $this->c($k ?: df_caller_f(), $required);
		return $k ? $this->req($k) : $d;
	}

	/**
	 * 2016-12-30
	 * @used-by \Df\PaypalClone\Webhook::logTitleSuffix()
	 * @param string|null $k [optional]
	 * @param string|null $d [optional]
	 * @return mixed
	 */
	final protected function cvo($k = null, $d = null) {return $this->cv($k ?: df_caller_f(), $d, false);}

	/**
	 * 2016-12-30
	 * @used-by testData()
	 * @see \Df\StripeClone\Webhook::defaultTestCase()
	 * @return string
	 */
	protected function defaultTestCase() {return 'confirm';}

	/**
	 * 2017-01-02
	 * @used-by \Dfe\AllPay\Webhook::test()
	 * @param string|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function extra($k = null, $d = null) {return dfak($this->_extra, $k, $d);}

	/**
	 * 2016-07-20
	 * @used-by ii()
	 * @see \Dfe\AllPay\Webhook\Offline::id()
	 * @return string
	 */
	protected function id() {return $this->idL2G($this->externalId());}

	/**
	 * 2016-07-10
	 * @used-by \Df\PaypalClone\Confirmation::capture()
	 * @return IOP|OP
	 */
	final protected function ii() {return dfc($this, function() {
		/** @var IOP|OP $result */
		$result = dfp_by_trans($this->tParent());
		dfp_trans_id($result, $this->id());
		return $result;
	});}

	/**
	 * 2016-07-20
	 * @used-by handle()
	 * @see \Df\StripeClone\Webhook::needCapture()
	 * @see \Dfe\AllPay\Webhook\BankCard::needCapture()
	 * @see \Dfe\AllPay\Webhook\Offline::needCapture()
	 * @see \Dfe\AllPay\Webhook\WebATM::needCapture()
	 * @return bool
	 */
	protected function needCapture() {return $this->c();}

	/**
	 * 2016-07-10
	 * @used-by \Df\PaypalClone\Confirmation::_handle()
	 * @return Order|DfOrder
	 */
	final protected function o() {return dfc($this, function() {
		/** @var Order|DfOrder $result */
		$result = $this->tParent()->getOrder();
		/**
		 * 2016-03-26
		 * Иначе будет создан новый объект payment.
		 * @used-by \Magento\Sales\Model\Order::getPayment()
		 */
		$result[IO::PAYMENT] = $this->ii();
		return $result;
	});}

	/**
	 * 2016-08-29
	 * Потомки перекрывают этот метод, когда ключ идентификатора запроса в запросе
	 * не совпадает с ключем идентификатора запроса в ответе.
	 * Так, в частности, происходит в модуле SecurePay:
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Webhook::parentIdKey()
	 *
	 * @uses \Df\PaypalClone\ICharge::requestIdKey()
	 * @used-by requestId()
	 * @return string
	 */
	protected function parentIdKey() {return df_con_s($this, 'Charge', 'requestIdKey');}

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @see \Dfe\AllPay\Webhook::resultSuccess()
	 * @return Result
	 */
	protected function resultSuccess() {return Text::i('success');}

	/**
	 * 2016-12-25
	 * @return S
	 */
	final protected function ss() {return dfc($this, function() {return S::conventionB(static::class);});}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	final protected function store() {return $this->o()->getStore();}

	/**
	 * 2017-01-02
	 * @used-by req()
	 * @see \Dfe\AllPay\Webhook::test()
	 * @return bool
	 */
	protected function test() {return !!$this->extra('test');}

	/**
	 * 2016-12-26
	 * @used-by log()
	 * @return string
	 */
	final protected function type() {return $this->cv(self::$typeKey, 'confirmation');}

	/**
	 * 2016-12-26
	 * @used-by log()
	 * @see \Dfe\AllPay\Webhook::typeLabel()
	 * @return string
	 */
	protected function typeLabel() {return $this->type();}

	/**
	 * 2016-08-27
	 * @used-by cv()
	 * @used-by statusExpected()
	 * @param string|null $k [optional]
	 * @param bool $required [optional]
	 * @return mixed|null
	 */
	protected function c($k = null, $required = true) {return
		dfc($this, function($k, $required = true) {
			/** @var mixed|null $result */
			$result = dfa($this->configCached(), $k);
			if ($required) {
				static::assertKeyIsDefined($k, $result);
			}
			return $result;
		}, [$k ?: df_caller_f(), $required])
	;}

	/**
	 * 2017-01-02
	 * @used-by \Df\Payment\Webhook::log()
	 * @see \Df\PaypalClone\Webhook::logTitleSuffix()
	 * @return string|null
	 */
	protected function logTitleSuffix() {return null;}

	/**
	 * 2016-08-27
	 * @used-by c()
	 * @return array(string => mixed)
	 */
	private function configCached() {return dfc($this, function() {return $this->config();});}

	/**
	 * 2016-07-11
	 * @used-by ii()
	 * @used-by parentIdG()
	 * @param string $localId
	 * @return string
	 * @uses \Df\Payment\Method::transactionIdL2G()
	 */
	private function idL2G($localId) {return dfp_method_call_s($this, 'transactionIdL2G', $localId);}

	/**
	 * 2016-12-26
	 * @used-by handle()
	 * @used-by resultError()
	 * @param \Exception|null $e [optional]
	 * @return void
	 */
	private function log(\Exception $e = null) {
		/** @var string $data */
		$data = df_json_encode_pretty($this->req());
		/** @var string $method */
		$code = dfp_method_code($this);
		/** @var string $title */
		$title = dfp_method_title($this);
		/** @var \Exception|string $v */
		/** @var string $suffix */
		if ($e) {
			list($v, $suffix) = [$e, 'exception'];
		}
		else {
			/** @var string $type */
			$type = $this->typeLabel();
			$v = df_ccc(': ', sprintf("[%s] {$type}", $title), $this->logTitleSuffix());
			$suffix = df_fs_name($type);
		}
		df_sentry_m()->user_context(['id' => $title]);
		df_sentry($v, [
			'extra' => ['Payment Data' => $data, 'Payment Method' => $title]
			,'tags' => ['Payment Method' => $title]
		]);
		df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  '.log', $data);
	}

	/**
	 * 2016-08-14
	 * @return Method
	 */
	private function m() {return dfc($this, function() {return
		df_ar($this->ii()->getMethodInstance(), Method::class)
	;});}

	/**
	 * 2016-07-10
	 * @used-by tParent()
	 * @return string
	 */
	private function parentIdG() {return dfc($this, function() {return
		$this->idL2G($this->parentId())
	;});}

	/**
	 * 2016-07-12
	 * @used-by req()
	 * @return array(string => string)
	 */
	private function testData() {
		/** @var string|null $case */
		$case = $this->extra('case');
		/** @var string $classSuffix */
		$classSuffix = df_class_last($this);
		/**
		 * 2016-08-28
		 * Если у класса Webhook нет подклассов,
		 * то не используем суффикс Webhook в именах файлах тестовых данных,
		 * а случай confirm делаем случаем по умолчанию.
		 * /dfe-allpay/confirm/?class=BankCard => AllPay/BankCard.json
		 * /dfe-allpay/confirm/?class=BankCard&case=failure => AllPay/BankCard-failure.json
		 * /dfe-securepay/confirm/?dfTest=1 => SecurePay/confirm.json
		 */
		if ($classSuffix === df_class_last(__CLASS__)) {
			$classSuffix = null;
			$case = $case ?: $this->defaultTestCase();
		}
		/** @var string $basename */
		$basename = df_ccc('-', $classSuffix, $case);
		/** @var string $module */
		$module = df_module_name_short($this);
		/** @var string $file */
		$file = BP . df_path_n_real("/_my/test/{$module}/{$basename}.json");
		if (!file_exists($file)) {
			df_error("Please place the webhook's test data to the «%s» file.", $file);
		}
		return df_json_decode(file_get_contents($file));
	}

	/**
	 * 2016-07-10
	 * 2016-12-30
	 * Возвращает транзакцию Magento, породившую данное оповещение от платёжной системы (webhook event).
	 * В то же время не каждое оповещение от платёжной системы инициируется запросом от Magento:
	 * например, оповещение могло быть инициировано некими действиями администратора магазина
	 * в административном интерфейсе магазина в платёжной системе.
	 * Однако первичная транзакция всё равно должна в Magento присутствовать.
	 * @return T
	 */
	private function tParent() {return dfc($this, function() {return
		df_load(T::class, $this->parentIdG(), true, 'txn_id')
	;});}

	/**
	 * 2017-01-02
	 * @used-by __construct()
	 * @used-by extra()
	 * @var array(string => mixed)
	 */
	private $_extra;

	/**
	 * 2017-01-02
	 * @used-by __construct()
	 * @used-by req()
	 * @var array(string => mixed)
	 */
	private $_req;

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @used-by \Df\Payment\Action\Webhook::error()
	 * @see \Dfe\AllPay\Webhook::resultError()
	 * @param \Exception $e
	 * @return Text
	 */
	public static function resultError(\Exception $e) {return
		Text::i(df_lets($e))->setHttpResponseCode(500)
	;}

	/**
	 * 2016-08-27
	 * 2016-12-31
	 * Название ключа в сообщении от платёжной системы,
	 * содержащего идентификатор платежа в платёжной системе.
	 * @used-by externalId()
	 * @var string
	 */
	protected static $externalIdKey = 'externalIdKey';

	/**
	 * 2016-08-27
	 * @var string
	 */
	protected static $needCapture = 'needCapture';

	/**
	 * 2016-12-26
	 * @var string
	 */
	protected static $typeKey = 'typeKey';

	/**
	 * 2016-12-30
	 * @used-by c()
	 * @param string $key
	 * @param mixed $value
	 * @throws DFE
	 */
	private static function assertKeyIsDefined($key, $value) {
		if (is_null($value)) {
			df_error("The class %s should define a value for the parameter «%s».",
				static::class, $key
			);
		}
	}
}