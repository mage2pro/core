<?php
namespace Df\Payment\W;
use Df\Payment\Method as M;
use Df\Payment\W\Exception\Critical;
/**
 * 2017-03-09
 * @see \Df\PaypalClone\W\Event
 * @see \Df\StripeClone\W\Event
 */
abstract class Event implements IEvent {
	/**
	 * 2017-01-16
	 * @used-by pid()
	 * @see \Df\StripeClone\W\Event::k_pid()
	 * @see \Df\GingerPaymentsBase\W\Event::k_pid()
	 * @see \Dfe\AllPay\W\Event::k_pid()
	 * @see \Dfe\IPay88\W\Event::k_pid()
	 * @see \Dfe\Robokassa\W\Event::k_pid()
	 * @see \Dfe\SecurePay\W\Event::k_pid()
	 * @return string
	 */
	abstract protected function k_pid();

	/**
	 * 2017-03-10
	 * @used-by \Df\Payment\W\F::event()
	 * @param Reader $r
	 */
	final function __construct(Reader $r) {$this->_r = $r;}

	/**
	 * 2017-01-02
	 * @used-by \Df\Payment\W\Handler::log()
	 * @see \Df\PaypalClone\W\Event::logTitleSuffix()
	 * @return string|null
	 */
	function logTitleSuffix() {return null;}

	/**
	 * 2017-03-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @used-by \Df\Payment\W\Nav::mPartial()
	 * @return M
	 */
	function m() {return $this->_r->m();}

	/**
	 * 2016-07-09
	 * 2017-01-04
	 * 2017-03-16
	 * Возвращает некую основу для вычисления идентификатора родительской транзакции в Magento.
	 * Эта основа в настоящее время бывает 2-х видов:
	 *
	 * 1) Идентификатор платежа в платёжной системе.
	 * Так происходит для Stripe-подобных модулей.
	 * На основе этого идентификатора мы:
	 *     1.1) вычисляем идентификатор родительской транзакции
	 *     (посредством прибавления окончания «-<тип родительской транзакции>»)
	 *     1.2) создаём идентификатор текущей транзакции
	 *     (аналогично, посредством прибавления окончания «-<тип текущей транзакции>»).
	 *
	 * 2) Переданный нами ранее платёжной системе наш внутренний идентификатор родительской транзакции
	 * (т.е., запроса к платёжой системе) в локальном (коротком) формате
	 * (т.е. без приставки «<имя платёжного модуля>-»).
	 *
	 * @used-by \Df\Payment\W\Nav::pid()
	 * @used-by \Df\PaypalClone\W\Event::idE()
	 * @used-by \Df\StripeClone\W\Event::idBase()
	 * @used-by \Dfe\Robokassa\W\Handler::result()
	 * @return string
	 */
	final function pid() {return $this->rr($this->k_pid());}

	/**
	 * 2017-03-10
	 * @override
	 * @see \Df\Payment\W\IEvent::r()
	 * @used-by \Df\Payment\W\Handler::r()
	 * @used-by \Df\Payment\W\Exception::r()
	 * @used-by \Dfe\IPay88\W\Event::option()
	 * @used-by \Dfe\Robokassa\W\Event::optionTitle()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final function r($k = null, $d = null) {return $this->_r->r($k, $d);}

	/**
	 * 2017-03-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @return Reader
	 */
	function rd() {return $this->_r;}

	/**
	 * 2017-01-12
	 * @used-by \Df\Payment\W\Handler::rr()
	 * @used-by \Df\PaypalClone\W\Event::idE()
	 * @used-by \Df\PaypalClone\W\Event::signatureProvided()
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
	 * @used-by \Dfe\AllPay\Choice::title()
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
	 * @used-by __construct()
	 * @used-by r()
	 * @used-by rd()
	 * @used-by t()
	 * @var Reader
	 */
	private $_r;
}