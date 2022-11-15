<?php
namespace Df\Zf;
/** @see \Df\Zf\Validate\Type */
abstract class Validate implements \Zend_Validate_Interface {
	/**
	 * @used-by self::getMessage()
	 * @see \Df\Zf\Validate\Type::_message()
	 */
	abstract protected function _message():string;

	/**
	 * @used-by \Df\Zf\Validate\ArrayT::s()
	 * @used-by \Df\Zf\Validate\IntT::s()
	 * @used-by \Df\Zf\Validate\StringT::s()
	 * @used-by \Df\Zf\Validate\StringT\IntT::s()
	 * @used-by \Df\Zf\Validate\StringT\Iso2::s()
	 * @used-by \Df\Zf\Validate\StringT\FloatT::s()
	 * @param array(string => mixed) $p
	 */
	final function __construct(array $p = []) {$this->_params = $p;}

	/**
	 * @used-by df_float()
	 * @used-by df_int()
	 * @used-by self::getMessages()
	 */
	final function getMessage():string {
		if (!isset($this->_message)) {
			$this->_message = $this->_message();
		}
		return $this->_message;
	}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::getMessages()
	 * @return array(string => string)
	 */
	final function getMessages():array {return [__CLASS__ => $this->getMessage()];}

	/**
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return mixed
	 */
	final protected function getValue() {return dfa($this->_params, self::$PARAM__VALUE);}

	/**
	 * @param string $message
	 */
	protected function setMessage($message) {$this->_message = $message;}

	/**
	 * @used-by \Df\Zf\Validate\ArrayT::isValid()
	 * @used-by \Df\Zf\Validate\IntT::isValid()
	 * @used-by \Df\Zf\Validate\StringT::isValid()
	 * @used-by \Df\Zf\Validate\StringT\FloatT::isValid()
	 * @used-by \Df\Zf\Validate\StringT\IntT::isValid()
	 * @used-by \Df\Zf\Validate\StringT\Iso2::isValid()
	 * @used-by \Df\Zf\Validate\StringT\Parser::isValid()
	 * @param mixed $v
	 */
	final protected function setValue($v):void {
		$this->reset();
		$this->_params[self::$PARAM__VALUE] = $v;
	}

	/** @used-by setValue() */
	private function reset():void {
		unset($this->_message);
		/**
		 * Раньше тут стоял код $this->_params = []
		 * который сбрасывает сразу все значения параметров.
		 * Однако этот код неверен!
		 * Негоже родительскому классу безапелляционно решать за потомков,
		 * какие данные им сбрасывать.
		 * Например, потомок @see \Df\Zf\Validate\Class
		 * хранит в параметре @see \Df\Zf\Validate\Class::$PARAM__CLASS
		 * требуемый класс результата,
		 * и сбрасывать это значение между разными валидациями не нужно!
		 * Вместо сброса значения между разными валидациями
		 * класс @see \Df\Zf\Validate\Class ведёт статический кэш своих экземпляров
		 * для каждого требуемого класса результата:
		 * @see \Df\Zf\Validate\Class::s().
		 * Сброс значения параметра @see \Df\Zf\Validate\Class::$PARAM__CLASS
		 * не только не нужен, но и приведёт к сбою!
		 * Пусть потомки сами решают
		 * посредством перекрытия метода @see \Df\Zf\Validate\Type::reset(),
		 * значения каких параметров им надо сбрасывать между разными валидациями.
		 */
		unset($this->_params[self::$PARAM__VALUE]);
	}

	/**
	 * @used-by self::getMessage()
	 * @used-by self::reset()
	 * @used-by self::setMessage()
	 * @var string
	 */
	private $_message;

	/**
	 * @used-by self::__construct()
	 * @used-by self::cfg()
	 * @used-by self::reset()
	 * @var array(string => mixed)
	 */
	private $_params = [];

	/** @var string */
	private static $PARAM__VALUE = 'value';
}