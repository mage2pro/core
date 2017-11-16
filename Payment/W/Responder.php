<?php
namespace Df\Payment\W;
use Df\Framework\W\Response;
use Df\Framework\W\Response\Text;
use Df\Payment\W\Exception\Ignored;
use Magento\Framework\Phrase;
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
	 * @param F $f
	 */
	final function __construct(F $f) {$this->_f = $f;}

	/**
	 * 2017-09-13
	 * @used-by \Df\Payment\W\Action::execute()
	 * @return Response
	 */
	final function get() {return $this->_response ?: $this->success();}

	/**
	 * 2017-09-13
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @param \Exception $e
	 */
	final function setError(\Exception $e) {$this->set($this->error($e));}

	/**
	 * 2017-09-13
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @param string|null $message [optional]
	 */
	final function setNotForUs($message = null) {$this->set($this->notForUs($message));}

	/**
	 * 2017-01-17
	 * @used-by \Df\Payment\W\Action::execute()
	 * @param Ignored $e
	 */
	final function setIgnored(Ignored $e) {$this->set(Text::i($e->message()));}

	/**
	 * 2017-09-13
	 * @used-by \Df\Payment\W\Strategy::softFailure()
	 * @param Response|Phrase|string|null $v
	 */
	final function setSoftFailure($v) {$this->set(
		($v = is_string($v) ?  __($v) : $v) instanceof Phrase ? Text::i($v) : $v
	);}

	/**
	 * 2017-09-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\Robokassa\W\Responder::success()
	 * @used-by \Dfe\YandexKassa\W\Responder::error()
	 * @used-by \Dfe\YandexKassa\W\Responder::success()
	 * @return Event
	 */
	protected function e() {return $this->_f->e();}

	/**
	 * 2017-09-13
	 * @used-by setError()
	 * @see \Dfe\AllPay\W\Responder::error()
	 * @see \Dfe\Qiwi\W\Responder::error()
	 * @param \Exception $e
	 * @return Response
	 */
	protected function error(\Exception $e) {return self::defaultError($e);}

	/**
	 * 2017-01-04
	 * @used-by setNotForUs
	 * @see \Dfe\AllPay\W\Responder::notForUs()
	 * @see \Dfe\Qiwi\W\Responder::notForUs()
	 * @param string|null $message [optional]
	 * @return Response
	 */
	protected function notForUs($message = null) {return Text::i($message);}

	/**
	 * 2017-09-13
	 * @used-by error()
	 * @used-by ignored()
	 * @used-by notForUs()
	 * @param Response $v
	 */
	final protected function set(Response $v) {$this->_response = $v;}

	/**
	 * 2017-09-13
	 * @used-by get()
	 * @see \Dfe\AllPay\W\Responder::success()
	 * @see \Dfe\Dragonpay\W\Responder::success()
	 * @see \Dfe\IPay88\W\Responder::success()
	 * @see \Dfe\Qiwi\W\Responder::success()
	 * @see \Dfe\Robokassa\W\Responder::success()
	 * @return Response
	 */
	protected function success() {return Text::i('success');}

	/**
	 * 2017-09-13
	 * @used-by __construct()
	 * @used-by e()
	 * @var F
	 */
	private $_f;

	/**
	 * 2017-09-13
	 * @used-by get()
	 * @used-by set()
	 * @var Response
	 */
	private $_response;

	/**
	 * 2017-09-13
	 * @used-by error()
	 * @used-by \Df\Payment\W\Action::execute()
	 * @param \Exception $e
	 * @return Response
	 */
	final static function defaultError(\Exception $e) {return Text::i(df_lets($e))->setHttpResponseCode(500);}
}