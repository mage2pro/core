<?php
namespace Df\Payment\W;
/**
 * 2017-03-10
 * @see \Df\Payment\W\Exception\Critical
 * @see \Df\Payment\W\Exception\Ignored
 */
abstract class Exception extends \Df\Payment\Exception {
	/**
	 * 2017-03-10
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @param string $m		Класс наследника \Df\Payment\Method
	 * @param IEvent $event
	 * @param mixed ...$args
	 */
	function __construct($m, IEvent $event, ...$args) {
		$this->_event = $event; $this->_m = $m; parent::__construct(df_format(...$args));
	}

	/**
	 * 2017-03-11
	 * @used-by \Df\Payment\W\Action::ignored()
	 * @return IEvent
	 */
	final function event() {return $this->_event;}

	/**
	 * 2017-03-11
	 * @used-by \Df\Payment\W\Action::ignored()
	 * @return string
	 */
	final function m() {return $this->_m;}

	/**
	 * 2017-03-11
	 * @used-by \Df\Payment\W\Action::ignored
	 * @used-by \Df\Payment\W\Exception\Ignored::mTitle()
	 * @return string
	 */
	final function mTitle() {return dfp_method_title($this->_m);}

	/**
	 * 2017-03-11
	 * @used-by __construct()
	 * @used-by event()
	 * @var IEvent
	 */
	private $_event;

	/**
	 * 2017-03-10
	 * @used-by __construct()
	 * @used-by m()
	 * @var string
	 */
	private $_m;
}