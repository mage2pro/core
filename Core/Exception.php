<?php
namespace Df\Core;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase;
use \Exception as E;
use \Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2016-07-31
 * Унаследовал наш класс от @see \Magento\Framework\Exception\LocalizedException
 * вместо @see E, потому что во многих местах ядро анализирует тип исключительной ситуации,
 * и по разному её обрабатывает, в зависимости от типа.
 * В частности, если исключительная ситуация имеет тип @see \Magento\Framework\Exception\LocalizedException
 * то ядро может показать её сообщение на экране, а в противном случае — не показать.
 * Бывает ещё, что в противном случае сообщение всё-таки показывается, но с другим форматированием.
 * @see \Df\API\Exception
 * @see \Dfe\GoogleFont\Exception
 * @see \Df\Payment\Exception
 * @see \Df\Payment\W\Exception\NotForUs
 * @see \Dfe\FacebookLogin\Exception
 */
class Exception extends LE implements \ArrayAccess {
	/**
	 * PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя: http://3v4l.org/qQdJ3
	 * @see \Df\API\Exception\HTTP::__construct()
	 * @see \Df\API\Response\Validator::__construct()
	 * @used-by df_error_create()
	 * @used-by self::wrap()
	 * @param mixed ...$a
	 */
	function __construct(...$a) {
		$a0 = dfa($a, 0); /** @var string|Phrase|E|array(string => mixed)|null $a0 */
		$prev = null; /** @var E|LE|null $prev */
		$m = null;  /** @var Phrase|null $m */
		# 2015-10-10
		if (is_array($a0)) {
			$this->_data = $a0;
		}
		elseif (df_is_phrase($a0)) {
			$m = $a0;
		}
		elseif (df_is_th($a0)) {
			$prev = df_th2x($a0);
		}
		elseif (is_string($a0)) {
			$m = __($a0);
		}
		$a1 = dfa($a, 1); /** @var int|string|E|Phrase|null $a1 */
		if (null !== $a1) {
			if (df_is_th($a1)) {
				$prev = $a1;
			}
			elseif (is_int($prev)) {
				$this->_stackLevelsCountToSkip = $a1;
			}
		}
		if (null == $m) {
			$m = __($prev ? df_xts($prev) : 'No message');
			# 2017-02-20 To faciliate the «No message» diagnostics.
			if (!$prev) {
				df_bt_log();
			}
		}
		parent::__construct($m, $prev);
	}

	/**
	 * 2014-05-20
	 * 1) "Provide an ability to specify a context for a `Df\Core\Exception` instance":
	 * https://github.com/mage2pro/core/issues/375
	 * 2) I implemented it by analogy with @see \Df\Core\O::a()
	 * @used-by df_error_create()
	 * @used-by self::sentryContext()
	 * @used-by \Df\Core\Exception::wrap()
	 * @param string|string[] $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final function context($k = '', $d = null) {return dfa($this->_context, $k, $d);}

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
	 * использовать функцию @see df_xts()
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
	 * @used-by df_xts()
	 * @see \Dfe\GoogleFont\Exception::message()
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
	 * @used-by df_xtsd()
	 * @used-by self::messageSentry()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @used-by \Df\Qa\Failure\Exception::main()
	 * @see \Df\PaypalClone\W\Exception\InvalidSignature::messageD()
	 * @see \Dfe\Omise\Exception\Charge::messageD()
	 */
	function messageD():string {return $this->message();}

	/**
	 * 2015-10-10
	 * 2024-05-20 @deprecated It is unused.
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
	 * @used-by \Dfe\FacebookLogin\Exception::message()
	 * @used-by \Dfe\GoogleFont\Exception::message()
	 * @param string $offset
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	function offsetGet($offset) {return dfa($this->_data, $offset);}

	/**
	 * 2015-10-10
	 * 2024-05-20 @deprecated It is unused.
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param mixed $value
	 */
	function offsetSet($offset, $value):void {$this->_data[$offset] = $value;}

	/**
	 * 2015-10-10
	 * 2024-05-20 @deprecated It is unused.
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
	function sentryContext():array {return ['extra' => $this->context()];}

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
     * @used-by \Dfe\GoogleFont\Fonts::responseA()
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
	 * 2014-05-20
	 * "Provide an ability to specify a context for a `Df\Core\Exception` instance":
	 * https://github.com/mage2pro/core/issues/375
	 * @used-by self::context()
	 * @var array(string => mixed)
	 */
	private $_context;

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
	 * 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	 * @used-by df_error_create()
	 * @used-by \Df\Qa\Failure\Exception::i()
	 */
	final static function wrap(Th $r, array $d = []):self {
		if (!$r instanceof self) {
			$r = new self($r);
		}
		# 2024-05-20 "Provide an ability to specify a context for a `Df\Core\Exception` instance":
		# https://github.com/mage2pro/core/issues/375
		$r->context($d);
		return $r;
	}
}