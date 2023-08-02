<?php
namespace Df\Payment\W;
use Df\Framework\W\Result as wResult;
use Df\Framework\W\Result\Text;
use Df\Payment\W\Exception\Ignored;
use Magento\Framework\Phrase;
use \Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2017-09-12
 * @see \Dfe\AllPay\W\Responder
 * @see \Dfe\Dragonpay\W\Responder
 * @see \Dfe\IPay88\W\Responder
 * @see \Dfe\Qiwi\W\Responder
 * @see \Dfe\Robokassa\W\Responder
 * @see \Dfe\YandexKassa\W\Responder
 */
class Responder {
	/**
	 * 2017-09-12
	 * @used-by \Df\Payment\W\F::responder()
	 */
	final function __construct(F $f) {$this->_f = $f;}

	/**
	 * 2017-09-13
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	final function get():wResult {return $this->isSuccess() ? $this->success() : $this->_response;}

	/**
	 * 2017-11-18
	 * @used-by self::get()
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	final function isSuccess():bool {return !$this->_response;}

	/**
	 * 2017-09-13
	 * 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @param Th|string $t
	 */
	final function setError($t):void {$this->set($this->error($t));}

	/**
	 * 2017-09-13
	 * @used-by \Df\Payment\W\Handler::handle()
	 */
	final function setNotForUs(string $m):void {$this->set($this->notForUs($m));}

	/**
	 * 2017-01-17
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	final function setIgnored(Ignored $e):void {$this->set(Text::i($e->message()));}

	/**
	 * 2017-09-13
	 * @used-by \Df\Payment\W\Strategy::softFailure()
	 * @param wResult|Phrase|string|null $v
	 */
	final function setSoftFailure($v):void {$this->set(
		($v = is_string($v) ?  __($v) : $v) instanceof Phrase ? Text::i($v) : $v
	);}

	/**
	 * 2017-09-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\Robokassa\W\Responder::success()
	 * @used-by \Dfe\YandexKassa\W\Responder::error()
	 * @used-by \Dfe\YandexKassa\W\Responder::success()
	 */
	protected function e():Event {return $this->_f->e();}

	/**
	 * 2017-09-13
	 * 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	 * @used-by self::setError()
	 * @see \Dfe\AllPay\W\Responder::error()
	 * @see \Dfe\Qiwi\W\Responder::error()
     * @see \Dfe\YandexKassa\W\Responder::error()
	 * @param Th|string $t
	 */
	protected function error($t):wResult {return self::defaultError($t);}

	/**
	 * 2017-01-04
	 * @used-by self::setNotForUs()
	 * @see \Dfe\AllPay\W\Responder::notForUs()
	 * @see \Dfe\Qiwi\W\Responder::notForUs()
     * @see \Dfe\YandexKassa\W\Responder::notForUs()
	 */
	protected function notForUs(string $m):wResult {return Text::i($m);}

	/**
	 * 2017-09-13
	 * @used-by self::error()
	 * @used-by self::ignored()
	 * @used-by self::notForUs()
	 */
	final protected function set(wResult $v):void {$this->_response = $v;}

	/**
	 * 2017-09-13
	 * @used-by self::get()
	 * @see \Dfe\AllPay\W\Responder::success()
	 * @see \Dfe\Dragonpay\W\Responder::success()
	 * @see \Dfe\IPay88\W\Responder::success()
	 * @see \Dfe\Qiwi\W\Responder::success()
	 * @see \Dfe\Robokassa\W\Responder::success()
     * @see \Dfe\YandexKassa\W\Responder::success()
	 */
	protected function success():wResult {return Text::i('success');}

	/**
	 * 2017-09-13
	 * @used-by self::__construct()
	 * @used-by self::e()
	 * @var F
	 */
	private $_f;

	/**
	 * 2017-09-13
	 * @used-by self::get()
	 * @used-by self::isSuccess()
	 * @used-by self::set()
	 * @var wResult
	 */
	private $_response;

	/**
	 * 2017-09-13
	 * 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	 * @used-by self::error()
	 * @used-by \Df\Payment\W\Action::execute()
	 * @param Th|string $t
	 */
	final static function defaultError($t):wResult {return Text::i(df_lxts($t))->setHttpResponseCode(500);}
}