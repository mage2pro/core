<?php
namespace Df\Core;
use \Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase;
/**
 * 2016-07-31
 * Унаследовал наш класс от @see \Magento\Framework\Exception\LocalizedException
 * вместо @see E, потому что во многих местах ядро анализирует тип исключительной ситуации,
 * и по разному её обрабатывает, в зависимости от типа.
 * В частности, если исключительная ситуация имеет тип
 * @see \Magento\Framework\Exception\LocalizedException
 * то ядро может показать её сообщение на экране, а в противном случае — не показать.
 * Бывает ещё, что в противном случае сообщение всё-таки показывается, но с другим форматированием.
 * @see \Df\API\Exception
 * @see \Df\GoogleFont\Exception
 * @see \Df\Payment\Exception
 * @see \Df\Payment\W\Exception\NotForUs
 * @see \Dfe\FacebookLogin\Exception
 */
class Exception extends LE implements \ArrayAccess {
	/**
	 * Обратите внимание, что PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя:
	 * http://3v4l.org/qQdJ3
	 * @see \Df\API\Exception\HTTP::__construct()
	 * @see \Df\API\Response\Validator::__construct()
	 * @used-by df_error_create()
	 * @param mixed ...$args
	 */
	function __construct(...$args) {
		$arg0 = dfa($args, 0); /** @var string|Phrase|E|array(string => mixed)|null $arg0 */
		$prev = null; /** @var E|LE|null $prev */
		$m = null;  /** @var Phrase|null $m */
		# 2015-10-10
		if (is_array($arg0)) {
			$this->_data = $arg0;
		}
		elseif ($arg0 instanceof Phrase) {
			$m = $arg0;
		}
		elseif (is_string($arg0)) {
			$m = __($arg0);
		}
		elseif ($arg0 instanceof E) {
			$prev = $arg0;
		}
		$arg1 = dfa($args, 1); /** @var int|string|E|Phrase|null $arg1 */
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
		if (is_null($m)) {
			$m = __($prev ? df_ets($prev) : 'No message');
			# 2017-02-20 To facilite the «No message» diagnostics.
			if (!$prev) {
				df_bt_log();
			}
		}
		parent::__construct($m, $prev);
	}

	/**
	 * @used-by self::__construct()
	 * @param mixed ...$args
	 */
	final function comment(...$args):void {$this->_comments[]= df_format($args);}

	/**
	 * @used-by \Df\Qa\Failure\Exception::preface()
	 * @return string[]
	 */
	final function comments():array {return $this->_comments;}

	/**
	 * @used-by \Df\Qa\Failure\Exception::stackLevel()
	 */
	final function getStackLevelsCountToSkip():int {return $this->_stackLevelsCountToSkip;}

	/**
	 * 2016-07-31
	 * @used-by \Df\Qa\Failure\Exception::main()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @see \Dfe\Omise\Exception\Charge::isMessageHtml()
	 */
	function isMessageHtml():bool {return $this->_messageIsHtml;}

	/**
	 * 2016-07-31
	 * @used-by df_error_html()
	 */
	final function markMessageAsHtml():self {$this->_messageIsHtml = true; return $this;}

	/**
	 * Стандартный метод @see E::getMessage() объявлен как final.
	 * Чтобы метод для получения диагностического сообщения
	 * можно было переопределять — добавляем свой.
	 *
	 * 2015-02-22
	 * Конечно, наша архитектура обладает тем недостатком,
	 * что пользователи нашего класса и его потомков должны для извлечения диагностического сообщения
	 * вместо стандартного интерфейса @see E::getMessage()
	 * использовать функцию @see df_ets()
	 *
	 * Однако неочевидно, как обойти этот недостаток.
	 * В частности, способ, когда диагностическое сообщение формируется прямо в конструкторе
	 * и передается первым параметром родительскому конструктору @see E::__construct()
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
	 * @see \Df\Payment\W\Exception\Ignored::message()
	 * @see \Dfe\FacebookLogin\Exception::message()
	 * @see \Dfe\Klarna\Exception::message()
	 * @see \Dfe\Omise\Exception\Charge::message()
	 * @see \Dfe\Stripe\Exception::message()
	 * @see \Dfe\TwoCheckout\Exception::message()
	 */
	function message():string {return $this->getMessage();}

	/**
	 * A message for the buyer.
	 * 2016-10-24
	 * Раньше этот метод возвращал `$this->message()`.
	 * Теперь я думаю, что '' логичнее:
	 * низкоуровневые сообщения покупателям показывать всегда неправильно,
	 * а потомки этого класса могут переопределить у себя этот метод
	 * (так, в частности, поступают потмки в платёжных модулях).
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @see \Dfe\CheckoutCom\Exception::messageC()
	 * @see \Dfe\Klarna\Exception::messageC()
	 * @see \Dfe\Omise\Exception\Charge::messageC()
	 * @see \Dfe\Stripe\Exception::messageC()
	 * @see \Dfe\TwoCheckout\Exception::messageC()
	 */
	function messageC():string {return '';}

	/**
	 * Сообщение для разработчика.
	 * @used-by df_etsd()
	 * @used-by self::messageSentry()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @used-by \Df\Qa\Failure\Exception::main()
	 * @used-by \Df\Sentry\Client::captureException()
	 * @see \Df\PaypalClone\W\Exception\InvalidSignature::messageD()
	 * @see \Dfe\Omise\Exception\Charge::messageD()
	 */
	function messageD():string {return $this->message();}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $offset
	 */
	function offsetExists($offset):bool {return isset($this->_data[$offset]);}

	/**
	 * 2015-10-10
	 * 2022-10-24
	 * 1) `mixed` as a return type is not supported by PHP < 8:
	 * https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * 2) `ReturnTypeWillChange` allows us to suppress the return type absence notice:
	 * https://github.com/mage2pro/core/issues/168#user-content-absent-return-type-deprecation
	 * https://github.com/mage2pro/core/issues/168#user-content-returntypewillchange
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	function offsetGet($offset) {return dfa($this->_data, $offset);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param mixed $value
	 */
	function offsetSet($offset, $value):void {$this->_data[$offset] = $value;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $offset
	 */
	function offsetUnset($offset):void {unset($this->_data[$offset]);}

	/**
	 * 2017-01-09
	 * @used-by df_sentry()
	 * @see \Dfe\Omise\Exception\Charge::sentryContext()
	 * @return array(string => mixed)
	 */
	function sentryContext():array {return [];}

	/**
	 * 2017-10-03
	 * @used-by \Df\Sentry\Client::captureException()
	 * @see \Df\PaypalClone\W\Exception\InvalidSignature::sentryType()
	 */
	function sentryType():string {return get_class($this);}

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
	 */
	function standard():E {return dfc($this, function() {return new E($this->message(), 0, $this);});}

	/**
	 * Цель этого метода — предоставить потомкам возможность
	 * указывать тип предыдущей исключительной ситуации в комментарии PHPDoc для потомка.
	 * Метод @uses E::getPrevious() объявлен как final,
	 * поэтому потомки не могут в комментариях PHPDoc указывать его тип: IntelliJ IDEA ругается.
	 */
	protected function prev():E {return $this->getPrevious();}

	/**
	 * @used-by self::comments()
	 * @var string[]
	 */
	private $_comments = [];

	/**
	 * 2015-10-10
	 * @used-by self::__construct()
	 * @used-by self::offsetExists()
	 * @used-by self::offsetGet()
	 * @used-by self::offsetUnset()
	 * @var array(string => mixed)
	 */
	private $_data = [];

	/**
	 * 2016-07-31
	 * @used-by self::isMessageHtml()
	 * @used-by self::markMessageAsHtml()
	 * @var bool
	 */
	private $_messageIsHtml = false;

	/**
	 * Количество последних элементов стека вызовов,
	 * которые надо пропустить как несущественные при показе стека вызовов в диагностическом отчёте.
	 * Это значение становится положительным,
	 * когда исключительная ситуация возбуждается не в момент её возникновения,
	 * а в некоей вспомогательной функции-обработчике, вызываемой в сбойном участке:
	 * @var int
	 */
	private $_stackLevelsCountToSkip = 0;

	/**
	 * @used-by df_error_create()
	 * @used-by \Df\Qa\Failure\Exception::i()
	 * @param E $e
	 */
	final static function wrap(E $e):self {return $e instanceof self ? $e : new self($e);}
}