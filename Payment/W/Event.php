<?php
namespace Df\Payment\W;
use Df\Payment\W\Reader as R;
use Df\Payment\W\Exception\Critical;
/**
 * 2017-03-09
 * @see \Dfe\AllPay\W\Event
 */
class Event implements IEvent {
	/**
	 * 2017-03-10
	 * @override
	 * @see \Df\Payment\W\IEvent::r()
	 * @used-by \Df\Payment\W\Handler::r()
	 * @used-by \Df\Payment\W\Exception::r()
	 * @param string|null $k
	 * @param string|null $d
	 * @return array(string => mixed)|mixed|null
	 */
	final function r($k = null, $d = null) {return $this->_r->r($k, $d);}

	/**
	 * 2017-03-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\Payment\W\F::c()
	 * @return Reader
	 */
	function reader() {return $this->_r;}

	/**
	 * 2017-01-12
	 * @used-by \Df\Payment\W\Handler::rr()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed
	 * @throws Critical
	 */
	final function rr($k = null, $d = null) {return $this->_r->rr($k, $d);}

	/**
	 * 2017-03-10
	 * 2017-03-13
	 * Returns a value in our internal format, not in the PSP format.
	 * @used-by tl()
	 * @used-by \Dfe\AllPay\Method::getInfoBlockType()
	 * @return string|null
	 */
	final function t() {return $this->_r->t();}

	/**
	 * 2017-03-10
	 * Type label.
	 * @override
	 * @see \Df\Payment\W\IEvent::r()
	 * @used-by \Df\Payment\W\Action::ignored()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @return string
	 */
	final function tl() {return dfc($this, function() {return $this->tl_(
		$this->useRawTypeForLabel() ? $this->_r->tRaw() : $this->t()
	);});}

	/**
	 * 2017-03-13
	 * @used-by tl()
	 * @see \Dfe\AllPay\W\Event::useRawTypeForLabel()
	 * @return bool
	 */
	protected function useRawTypeForLabel() {return false;}

	/**
	 * 2017-03-13
	 * @used-by tl()
	 * @used-by \Df\Payment\W\Event::tl_()
	 * @see \Dfe\AllPay\W\Event::tl_()
	 * @param string|null $t
	 * @return string
	 */
	protected function tl_($t) {return $this->_r->tl_($t);}

	/**
	 * 2017-03-10
	 * @used-by i()
	 * @used-by \Df\Payment\W\F::__construct()
	 * @param R $r
	 */
	private function __construct(R $r) {$this->_r = $r;}

	/**
	 * 2017-03-10
	 * @used-by __construct()
	 * @used-by r()
	 * @used-by reader()
	 * @used-by t()
	 * @var Reader
	 */
	private $_r;

	/**
	 * 2017-03-13
	 * 2017-03-14
	 * Нельзя использовать здесь @see df_newa(), потому что наш конструктор — приватный.
	 * @used-by \Df\Payment\W\F::event()
	 * @param string $class
	 * @param Reader $r
	 * @return self
	 */
	final static function i($class, R $r) {return df_ar(new $class($r), __CLASS__);}
}