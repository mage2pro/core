<?php
namespace Df\Payment\W;
/**
 * 2017-03-11
 * @see \Df\Payment\W\Event
 * @see \Df\Payment\W\Reader
 */
interface IEvent {
	/**
	 * 2017-03-11
	 * @see \Df\Payment\W\Event::r()
	 * @see \Df\Payment\W\Reader::r()
	 * @param string|null $k
	 * @param string|null $d
	 * @return array(string => mixed)|mixed|null
	 */
	function r($k = null, $d = null);

	/**
	 * 2017-03-11
	 * Type label.
	 * @used-by \Df\Payment\W\Action::ignoredLog()
	 * @see \Df\Payment\W\Event::tl()
	 * @see \Df\Payment\W\Reader::tl()
	 * @return string
	 */
	function tl();
}