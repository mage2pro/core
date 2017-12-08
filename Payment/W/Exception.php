<?php
namespace Df\Payment\W;
use Df\Payment\Method as M;
/**
 * 2017-03-10
 * @see \Df\Payment\W\Exception\Critical
 * @see \Df\Payment\W\Exception\Ignored
 */
abstract class Exception extends \Df\Payment\Exception {
	/**
	 * 2017-03-10
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @used-by \Df\Payment\W\F::c()
 	 * @used-by \Df\Payment\W\Reader::error()
	 * @see \Df\Payment\W\Exception\Ignored::__construct()
	 * @see \Df\PaypalClone\W\Exception\InvalidSignature::__construct()
	 * @param M $m
	 * @param IEvent $event
	 * @param mixed ...$args
	 */
	function __construct(M $m, IEvent $event, ...$args) {
		$this->_event = $event; $this->_m = $m; parent::__construct(df_format(...$args));
	}

	/**
	 * 2017-03-11
	 * @used-by \Df\Payment\W\Action::ignoredLog()
	 * @return IEvent
	 */
	final function event() {return $this->_event;}

	/**
	 * 2017-03-11
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @used-by \Df\Payment\W\Action::ignoredLog()
	 * @return string
	 */
	final function m() {return $this->_m;}

	/**
	 * 2017-03-11
	 * @used-by \Df\Payment\W\Action::ignored
	 * @used-by \Df\Payment\W\Exception\Ignored::mTitle()
	 * @return string
	 */
	final function mTitle() {return dfpm_title($this->_m);}

	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Core\Exception::module()
	 * @used-by \Df\Core\Exception::reportNamePrefix()
	 * @return string|object
	 */
	final protected function module() {return $this->_m;}

	/**
	 * 2017-03-11
	 * @used-by __construct()
	 * @used-by event()
	 * @var IEvent
	 */
	private $_event;

	/**
	 * 2017-03-10
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @used-by __construct()
	 * @used-by m()
	 * @var string
	 */
	private $_m;
}