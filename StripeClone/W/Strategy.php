<?php
namespace Df\StripeClone\W;
use Df\Sales\Model\Order as DFO;
use Df\StripeClone\Method as M;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-01-06
/** @see \Df\StripeClone\W\Strategy\Charge */
abstract class Strategy {
	/**
	 * 2017-01-06
	 * @used-by handle()
	 * @see \Df\StripeClone\W\Strategy\Charge\Authorized::_handle()
	 * @see \Df\StripeClone\W\Strategy\Charge\Captured::_handle()
	 * @see \Df\StripeClone\W\Strategy\Charge\Refunded::_handle()
	 * @return void
	 */
	abstract protected function _handle();

	/**
	 * 2017-03-18
	 * @used-by ro()
	 * @used-by ttCurrent()
	 * @used-by \Df\StripeClone\W\Strategy\Charge::action()
	 * @return Event
	 */
	final protected function e() {return $this->_h->e();}

	/**
	 * 2017-01-17
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Refunded::_handle()
	 * @return Handler
	 */
	final protected function h() {return $this->_h;}

	/**
	 * 2017-01-15
	 * @return M
	 */
	final protected function m() {return dfc($this, function() {return df_ar($this->_h->m(), M::class);});}

	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Authorized::_handle()
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Captured::_handle()
	 * @return O|DFO
	 */
	final protected function o() {return $this->_h->o();}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Authorized::_handle()
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Captured::_handle()
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Captured::invoice()
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Refunded::_handle()
	 * @return OP|null
	 */
	final protected function op() {return $this->_h->op();}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Captured::_handle()
	 * @param Result|Phrase|string $v
	 * @return void
	 */
	final protected function resultSet($v) {$this->_h->resultSet($v);}

	/**
	 * 2017-01-07
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Refunded::_handle()
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function ro($k = null, $d = null) {return $this->e()->ro($k, $d);}

	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\W\Handler::_handle()
	 * @param Handler $h
	 */
	private function __construct(Handler $h) {$this->_h = $h;}

	/**
	 * 2017-01-06
	 * @used-by __construct()
	 * @used-by m()
	 * @used-by o()
	 * @used-by resultSet()
	 * @var Handler
	 */
	private $_h;

	/**
	 * 2017-03-18
	 * @used-by \Df\StripeClone\W\Handler::_handle()
	 * @param string $class
	 * @param Handler $h
	 */
	final static function handle($class, Handler $h) {
		/** @var self $i */
		$i = df_ar(new $class($h), __CLASS__);
		$i->_handle();
	}
}