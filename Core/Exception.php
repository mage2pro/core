<?php
namespace Df\Core;
use \Exception as E;
use Df\Qa\Message\Failure\Exception as QE;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase;
/**
 * 2016-07-31
 * Унаследовал наш класс от @see \Magento\Framework\Exception\LocalizedException вместо @see \Exception,
 * потому что во многих местах ядро анализирует тип исключительной ситуации,
 * и по разному её обрабатывает, в зависимости от типа.
 * В частности, если исключительная ситуация имеет тип
 * @see \Magento\Framework\Exception\LocalizedException
 * то ядро может показать её сообщение на экране, а в противном случае — не показать.
 * Бывает ещё, что в противном случае сообщение всё-таки показывается, но с другим форматированием.
 * @see \Df\API\Exception
 * @see \Df\Payment\Exception
 */
class Exception extends LE implements \ArrayAccess {
	/**
	 * Обратите внимание, что PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя:
	 * http://3v4l.org/qQdJ3
	 * @see \Df\API\Exception\HTTP::__construct()
	 * @see \Df\API\Response\Validator::__construct()
	 * @param mixed ...$args
	 */
	function __construct(...$args) {
		/** @var string|Phrase|E|array(string => mixed)|null $arg0 */
		$arg0 = dfa($args, 0);
		/** @var E|LE|null $prev */
		$prev = null;
		/** @var Phrase|null $message */
		$message = null;
		// 2015-10-10
		if (is_array($arg0)) {
			$this->_data = $arg0;
		}
		elseif ($arg0 instanceof Phrase) {
			$message = $arg0;
		}
		elseif (is_string($arg0)) {
			$message = __($arg0);
		}
		elseif ($arg0 instanceof E) {
			$prev = $arg0;
		}
		/** @var int|string|E|Phrase|null $arg1 */
		$arg1 = dfa($args, 1);
		if (!is_null($arg1)) {
			if ($arg1 instanceof E) {
				$prev = $arg1;
			}
			elseif (is_int($prev)) {
				$this->_stackLevelsCountToSkip = $arg1;
			}
			elseif (is_string($arg1) || $arg1 instanceof Phrase) {
				$this->comment((string)$arg1);
			}
		}
		if (is_null($message)) {
			$message = __($prev ? df_ets($prev) : 'No message');
			// 2017-02-20 To facilite the «No message» diagnostics.
			if (!$prev) {
				df_bt();
			}
		}
		parent::__construct($message, $prev);
	}

	/**
	 * @used-by __construct()
	 * @used-by Df_Shipping_Collector::call()
	 * @used-by Df_Core_Validator::resolveForProperty()
	 * @param mixed ...$args
	 */
	function comment(...$args) {$this->_comments[]= df_format($args);}

	/**
	 * @param mixed ...$args
	 */
	function commentPrepend(...$args) {array_unshift($this->_comments, df_format($args));}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::preface()
	 * @return string[]
	 */
	function comments() {return $this->_comments;}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::stackLevel()
	 * @return int
	 */
	function getStackLevelsCountToSkip() {return $this->_stackLevelsCountToSkip;}

	/**
	 * К сожалению, не можем перекрыть @see \Exception::getTraceAsString(),
	 * потому что этот метод — финальный
	 * @return string
	 */
	function getTraceAsText() {return QE::i([QE::P__EXCEPTION => $this])->traceS();}

	/**
	 * 2016-07-31
	 * @used-by \Df\Qa\Message\Failure\Exception::main()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @see \Dfe\Omise\Exception\Charge::isMessageHtml()
	 * @return bool
	 */
	function isMessageHtml() {return $this->_messageIsHtml;}

	/**
	 * 2016-07-31
	 * @used-by df_error_create_html()
	 * @return $this
	 */
	function markMessageAsHtml() {$this->_messageIsHtml = true; return $this;}

	/**
	 * Стандартный метод @see \Exception::getMessage() объявлен как final.
	 * Чтобы метод для получения диагностического сообщения
	 * можно было переопределять — добавляем свой.
	 *
	 * 2015-02-22
	 * Конечно, наша архитектура обладает тем недостатком,
	 * что пользователи нашего класса и его потомков должны для извлечения диагностического сообщения
	 * вместо стандартного интерфейса @see \Exception::getMessage()
	 * использовать функцию @see df_ets()
	 *
	 * Однако неочевидно, как обойти этот недостаток.
	 * В частности, способ, когда диагностическое сообщение формируется прямо в конструкторе
	 * и передается первым параметром родительскому конструктору @see \Exception::__construct()
	 * не всегда подходит, потому что полный текст диагностического сообщения
	 * не всегда известен в момент вызова конструктора @see __construct().
	 * Пример, когда неизвестен: @see \Df\Core\Exception_Batch::message()
	 * (тот класс работает как контеёнер для других исключительных ситуаций,
	 * и полный текст диагностического сообщения
	 * получается объединением текстов элементом контейнера,
	 * которые добавляются динамически, а не сразу в конструкторе).
	 * По этой причине данный метод @see message(), несмотря на его некую громоздкость,
	 * нам действительно нужен.
	 * @used-by df_ets()
	 * @see \Df\GoogleFont\Exception::message()
	 * @see \Dfe\FacebookLogin\Exception::message()
	 * @see \Dfe\Klarna\Exception::message()
	 * @see \Dfe\Omise\Exception\Charge::message()
	 * @see \Dfe\Stripe\Exception::message()
	 * @see \Dfe\TwoCheckout\Exception::message()
	 * @return string
	 */
	function message() {return $this->getMessage();}

	/**
	 * A message for a buyer.
	 * 2016-10-24
	 * Раньше этот метод возвращал $this->message().
	 * Теперь я думаю, что null логичнее:
	 * низкоуровневые сообщения покупателям показывать всегда неправильно,
	 * а потомки этого класса могут переопределить у себя этот метод
	 * (так, в частности, поступают потмки в платёжных модулях).
	 * @see \Dfe\CheckoutCom\Exception::messageC()
	 * @see \Dfe\Klarna\Exception::messageC()
	 * @see \Dfe\Omise\Exception\Charge::messageC()
	 * @see \Dfe\Stripe\Exception::messageC()
	 * @see \Dfe\TwoCheckout\Exception::messageC()
	 * @return string|null
	 */
	function messageC() {return null;}

	/**
	 * Сообщение для разработчика.
	 * @used-by df_etsd()
	 * @used-by messageL()
	 * @used-by messageSentry()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @see \Df\PaypalClone\W\Exception\InvalidSignature::messageD()
	 * @return string
	 */
	function messageD() {return $this->message();}

	/**
	 * 2016-08-19
	 * Сообщение для журнала.
	 * @used-by \Df\Qa\Message\Failure\Exception::main()
	 * @return string
	 */
	function messageL() {return $this->messageD();}

	/**
	 * 2017-01-09
	 * Сообщение для Sentry.
	 * @used-by \Df\Sentry\Client::captureException()
	 * @see \Dfe\Omise\Exception\Charge::messageSentry()
	 * @return string
	 */
	function messageSentry() {return $this->messageD();}

	/**
	 * @return bool
	 */
	function needNotifyAdmin() {return true;}

	/**
	 * @return bool
	 */
	function needNotifyDeveloper() {return true;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $offset
	 * @return bool
	 */
	function offsetExists($offset) {return isset($this->_data[$offset]);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	 */
	function offsetGet($offset) {return dfa($this->_data, $offset);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param mixed $value
	 */
	function offsetSet($offset, $value) {$this->_data[$offset] = $value;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $offset
	 */
	function offsetUnset($offset) {unset($this->_data[$offset]);}

	/**
	 * 2016-10-24
	 * @used-by \Df\Qa\Message\Failure\Exception::reportNamePrefix()
	 * @return string|string[]
	 */
	final function reportNamePrefix() {return [df_module_name_lc($this->module()), 'exception'];}

	/**
	 * 2017-01-09
	 * @used-by df_sentry()
	 * @see \Dfe\Omise\Exception\Charge::sentryContext()
	 * @return array(string => mixed)
	 */
	function sentryContext() {return [];}

	/**
	 * 2017-10-03
	 * @used-by \Df\Sentry\Client::captureException()
	 * @see \Df\PaypalClone\W\Exception\InvalidSignature::sentryType()
	 * @return string
	 */
	function sentryType() {return get_class($this);}

	/**
	 * 2015-11-27
	 * Мы не можем перекрыть метод @see \Exception::getMessage(), потому что он финальный.
	 * С другой стороны, наш метод @see \Df\Core\Exception::message()
	 * не будет понят стандартной средой,
	 * и мы в стандартной среде не будем иметь диагностического сообщения вовсе.
	 * Поэтому если мы сами не в состоянии обработать исключительную ситуацию,
	 * то вызываем метод @see \Df\Core\Exception::standard().
	 * Этот метод конвертирует исключительную ситуацию в стандартную,
	 * и стандартная среда её успешно обработает.
	 * @return \Exception
	 */
	function standard() {return dfc($this, function() {return new \Exception(
		$this->message(), 0, $this
	);});}

	/**
	 * 2017-10-03
	 * The allowed results:
	 * 1) A module name. E.g.: «A_B».
	 * 2) A class name. E.g.: «A\B\C».
	 * 3) An object. It will be treated as case 2 after @see get_class()
	 * @used-by reportNamePrefix()
	 * @see \Df\Payment\W\Exception::module()
	 * @return string|object
	 */
	protected function module() {return $this;}

	/**
	 * Цель этого метода — предоставить потомкам возможность
	 * указывать тип предыдущей исключительной ситуации в комментарии PHPDoc для потомка.
	 * Метод @uses \Exception::getPrevious() объявлен как final,
	 * поэтому потомки не могут в комментариях PHPDoc указывать его тип: IntelliJ IDEA ругается.
	 * 2016-08-19
	 * @return E
	 */
	protected function prev() {return $this->getPrevious();}

	/**
	 * @used-by comments()
	 * @var string[]
	 */
	private $_comments = [];

	/**
	 * 2015-10-10
	 * @var array(string => mixed)
	 */
	private $_data = [];

	/**
	 * 2016-07-31
	 * @used-by isMessageHtml()
	 * @used-by markMessageAsHtml()
	 * @var bool
	 */
	private $_messageIsHtml = false;

	/**
	 * Количество последних элементов стека вызовов,
	 * которые надо пропустить как несущественные
	 * при показе стека вызовов в диагностическом отчёте.
	 * Это значение становится положительным,
	 * когда исключительная ситуация возбуждается не в момент её возникновения,
	 * а в некоей вспомогательной функции-обработчике, вызываемой в сбойном участке:
	 * @see Df_Qa_Method::throwException()
	 * @var int
	 */
	private $_stackLevelsCountToSkip = 0;

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::e()
	 * @used-by Df_Shipping_Collector::call()
	 * @param \Exception $e
	 * @return $this
	 */
	static function wrap(E $e) {return $e instanceof self ? $e : new self($e);}
}