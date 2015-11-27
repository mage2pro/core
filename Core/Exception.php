<?php
namespace Df\Core;
use Magento\Framework\Phrase;
class Exception extends \Exception implements \ArrayAccess {
	/**
	 * Обратите внимание, что PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя:
	 * http://3v4l.org/qQdJ3
	 * @return Exception
	 */
	public function __construct() {
		/** @var mixed $args */
		$args = func_get_args();
		/** @var string|Phrase|\Exception|array(string => mixed)|null $arg0 */
		$arg0 = df_a($args, 0);
		/** @var string|null $message */
		// 2015-10-10
		if (is_array($arg0)) {
			$this->_data = $arg0;
		}
		/**
		 * 2015-10-10
		 * По аналогии с @see \Magento\Framework\Exception\LocalizedException::__construct()
		 */
		else if (is_string($arg0) || $arg0 instanceof Phrase) {
			$message = (string)$arg0;
		}
		else if ($arg0 instanceof \Exception) {
			/** @used-by wrap() */
			$this->_internal = $arg0;
			/** Благодаря этому коду @see getMessage() вернёт сообщение внутреннего объекта. */
			$message = $this->_internal->getMessage();
		}
		/** @var int|string|\Exception|Phrase|null $arg1 */
		$arg1 = df_a($args, 1);
		/** @var \Exception|null $internalException */
		$internalException = null;
		if (!is_null($arg1)) {
			if ($internalException instanceof \Exception) {
				$internalException = $arg1;
			}
			else if (is_int($internalException)) {
				$this->_stackLevelsCountToSkip = $arg1;
			}
			else if (is_string($arg1) || $arg1 instanceof Phrase) {
				$this->comment((string)$arg1);
			}
		}
		parent::__construct(isset($message) ? $message : null, 0, $internalException);
	}

	/**
	 * @used-by __construct()
	 * @used-by Df_Shipping_Collector::call()
	 * @used-by Df_Core_Validator::resolveForProperty()
	 * @param string|Phrase $comment
	 * @return void
	 */
	public function comment($comment) {
		$args = func_get_args();
		$this->_comments[]= df_format($args);
	}

	/**
	 * @param string|Phrase $comment
	 * @return void
	 */
	public function commentPrepend($comment) {
		$args = func_get_args();
		array_unshift($this->_comments, df_format($args));
	}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::preface()
	 * @return string[]
	 */
	public function comments() {return $this->_comments;}

	/**
	 * Диагностическое сообщение для администратора интернет-магазина.
	 * @return string
	 */
	public function getMessageForAdmin() {return $this->getMessageRm();}

	/**
	 * Диагностическое сообщение для клиента интернет-магазина.
	 * @return string
	 */
	public function getMessageForCustomer() {return $this->getMessageRm();}

	/**
	 * Диагностическое сообщение для клиента интернет-магазина.
	 * @return string
	 */
	public function getMessageForDeveloper() {return $this->getMessageRm();}

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
	 * Пример, когда неизвестен: @see \Df\Core\Exception_Batch::getMessageRm()
	 * (тот класс работает как контеёнер для других исключительных ситуаций,
	 * и полный текст диагностического сообщения
	 * получается объединением текстов элементом контейнера,
	 * которые добавляются динамически, а не сразу в конструкторе).
	 * По этой причине данный метод @see getMessageRm(), несмотря на его некую громоздкость,
	 * нам действительно нужен.
	 * @used-by df_ets()
	 * @return string
	 */
	public function getMessageRm() {return $this->getMessage();}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::stackLevel()
	 * @return int
	 */
	public function getStackLevelsCountToSkip() {return $this->_stackLevelsCountToSkip;}

	/**
	 * К сожалению, не можем перекрыть @see \Exception::getTraceAsString(),
	 * потому что этот метод — финальный
	 * @return string
	 */
	public function getTraceAsText() {
		return \Df\Qa\Message\Failure\Exception::i(array(
			\Df\Qa\Message\Failure\Exception::P__EXCEPTION => $this
			,\Df\Qa\Message\Failure\Exception::P__NEED_LOG_TO_FILE => false
			,\Df\Qa\Message\Failure\Exception::P__NEED_NOTIFY_DEVELOPER => false
		))->traceS();
	}

	/**
	 * @used-by Df_Qa_Message_Failure_Exception::trace()
	 * @return array(array(string => string|int))
	 */
	public function getTraceRm() {
		return !$this->_internal ? parent::getTrace() : $this->_internal->getTrace();
	}

	/**
	 * @return bool
	 */
	public function needNotifyAdmin() {return true;}

	/**
	 * @return bool
	 */
	public function needNotifyDeveloper() {return true;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset) {return isset($this->_data[$offset]);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {return df_a($this->_data, $offset);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value) {$this->_data[$offset] = $value;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset) {unset($this->_data[$offset]);}

	/**
	 * 2015-11-27
	 * Мы не можем перекрыть метод @see \Exception::getMessage(), потому что он финальный.
	 * С другой стороны, наш метод @see \Df\Core\Exception::getMessageRm()
	 * не будет понят стандартной средой,
	 * и мы в стандартной среде не будем иметь диагностического сообщения вовсе.
	 * Поэтому если мы сами не в состоянии обработать исключительную ситуацию,
	 * то вызываем метод @see \Df\Core\Exception::standard().
	 * Этот метод конвертирует исключительную ситуацию в стандартную,
	 * и стандартная среда её успешно обработает.
	 * @return \Exception
	 */
	public function standard() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new \Exception($this->getMessageRm(), 0, $this);
		}
		return $this->{__METHOD__};
	}

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
	 * @var \Exception|null
	 */
	private $_internal;

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
	 * @return Exception
	 */
	public static function wrap(\Exception $e) {return $e instanceof self ? $e : new self($e);}
}