<?php
namespace Df\Payment\W;
/**
 * 2017-03-11
 * @used-by \Df\Payment\W\Exception::__construct()
 * @used-by \Df\Payment\W\Exception::event()
 * @see \Df\Payment\W\Event
 * @see \Df\Payment\W\Reader
 */
interface IEvent {
	/**
	 * 2017-03-11
	 * @see \Df\Payment\W\Event::r()
	 * @see \Df\Payment\W\Reader::r()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d
	 * @return array(string => mixed)|mixed|null
	 */
	function r($k = '', $d = null);

	/**
	 * 2017-03-11 Type label.
	 * @see \Df\Payment\W\Event::tl()
	 * @see \Df\Payment\W\Reader::tl()
	 */
	function tl():string;
}