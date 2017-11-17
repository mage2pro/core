<?php
namespace Df\Payment\W;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Df\Sales\Model\Order as DFO;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-01-06
 * @see \Df\Payment\W\Strategy\CapturePreauthorized
 * @see \Df\Payment\W\Strategy\ConfirmPending
 * @see \Df\Payment\W\Strategy\Refund
 * @see \Dfe\Stripe\W\Strategy\Charge3DS
 */
abstract class Strategy {
	/**
	 * 2017-01-06
	 * @used-by handle()
	 * @see \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
	 * @see \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @see \Df\Payment\W\Strategy\Refund::_handle()
	 */
	abstract protected function _handle();

	/**
	 * 2017-11-10
	 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 * @param string $c
	 */
	final protected function delegate($c) {self::handle($c, $this->_h);}

	/**
	 * 2017-03-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by ro()
	 * @used-by ttCurrent()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 * @return Event
	 */
	protected function e() {return $this->_h->e();}

	/**
	 * 2017-01-17
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 * @return Handler
	 */
	final protected function h() {return $this->_h;}

	/**
	 * 2017-01-15
	 * @used-by s()
	 * @return M
	 */
	final protected function m() {return dfc($this, function() {return df_ar($this->_h->m(), M::class);});}

	/**
	 * 2017-01-06
	 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @return O|DFO
	 */
	final protected function o() {return $this->_h->o();}

	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
	 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::invoice()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 * @return OP
	 */
	final protected function op() {return $this->_h->op();}

	/**
	 * 2017-11-17
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @return S
	 */
	final protected function s() {return $this->m()->s();}

	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @param Result|Phrase|string $v
	 */
	final protected function softFailure($v) {F::s($this->m())->responder()->setSoftFailure($v);}

	/**
	 * 2017-01-06
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @param Handler $h
	 */
	private function __construct(Handler $h) {$this->_h = $h;}

	/**
	 * 2017-01-06
	 * @used-by __construct()
	 * @used-by delegate()
	 * @used-by m()
	 * @used-by o()
	 * @var Handler
	 */
	private $_h;

	/**
	 * 2017-03-18
	 * @used-by delegate()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @param string $class
	 * @param Handler $h
	 */
	final static function handle($class, Handler $h) {
		$i = df_ar(new $class($h), __CLASS__); /** @var self $i */
		$i->_handle();
	}
}