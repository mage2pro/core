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
	 * @used-by self::handle()
	 * @see \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
	 * @see \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @see \Df\Payment\W\Strategy\Refund::_handle()
	 */
	abstract protected function _handle():void;

	/**
	 * 2017-11-10
	 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 */
	final protected function delegate(string $c):void {self::handle($c, $this->_h);}

	/**
	 * 2017-03-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by self::ro()
	 * @used-by self::ttCurrent()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 * @used-by \Dfe\TBCBank\W\Strategy\ConfirmPending::onSuccess()
	 */
	protected function e():Event {return $this->_h->e();}

	/**
	 * 2017-01-17
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 */
	final protected function h():Handler {return $this->_h;}

	/**
	 * 2017-01-15
	 * @used-by self::s()
	 */
	final protected function m():M {return dfc($this, function() {return df_ar($this->_h->m(), M::class);});}

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
	 */
	final protected function op():OP {return $this->_h->op();}

	/**
	 * 2017-11-17
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 */
	final protected function s():S {return $this->m()->s();}

	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @param Result|Phrase|string $v
	 */
	final protected function softFailure($v) {F::s($this->m())->responder()->setSoftFailure($v);}

	/**
	 * 2017-01-06
	 * @used-by self::handle()
	 * @param Handler $h
	 */
	private function __construct(Handler $h) {$this->_h = $h;}

	/**
	 * 2017-01-06
	 * @used-by self::__construct()
	 * @used-by self::delegate()
	 * @used-by self::m()
	 * @used-by self::o()
	 * @var Handler
	 */
	private $_h;

	/**
	 * 2017-03-18
	 * @used-by self::delegate()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @param string $class
	 * @param Handler $h
	 */
	final static function handle($class, Handler $h):void {
		$i = df_ar(new $class($h), __CLASS__); /** @var self $i */
		$i->_handle();
	}
}