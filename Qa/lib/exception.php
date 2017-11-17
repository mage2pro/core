<?php
use Df\Core\Exception as DFE;
use Df\Qa\Message\Failure\Exception as QE;
use Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase;
/**
 * 2016-07-18
 * @param E $e
 * @return E
 */
function df_ef(E $e) {while ($e->getPrevious()) {$e = $e->getPrevious();} return $e;}

/**
 * @used-by \Dfe\Square\Source\Location::exception()
 * @param E|string|Phrase $e
 * @return string|Phrase
 */
function df_ets($e) {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->message() : $e->getMessage())
);}

/**
 * 2016-10-24
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Klarna\T\Charge::t01()
 * @param E|string $e
 * @return string
 */
function df_etsd($e) {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->messageD() : $e->getMessage())
);}

/**
 * 2016-07-31
 * @param E $e
 * @return DFE
 */
function df_ewrap($e) {return DFE::wrap($e);}

/**
 * К сожалению, не можем перекрыть Exception::getTraceAsString(),
 * потому что этот метод — финальный
 *
 * @param E $exception
 * @param bool $showCodeContext [optional]
 * @return string
 */
function df_exception_get_trace(E $exception, $showCodeContext = false) {return
	QE::i([
		QE::P__EXCEPTION => $exception
		,QE::P__SHOW_CODE_CONTEXT => $showCodeContext
	])->traceS()
;}

/**
 * 2016-03-17
 * @param E $e
 * @return LE
 */
function df_le(E $e) {return $e instanceof LE ? $e : new LE(__(df_ets($e)), $e);}

/**
 * 2016-07-20
 * @used-by \Df\Payment\W\Responder::defaultError()
 * @used-by \Dfe\AllPay\W\Responder::error()
 * @param E|string $e
 * @return string|Phrase
 */
function df_lets($e) {return !$e instanceof E ? __($e) : df_ets(df_le($e));}

/**
 * 2016-03-17
 * @param callable $function
 * @return mixed
 * @throws LE
 */
function df_leh(callable $function) {
	/** @var mixed $result */
	try {$result = $function();}
	catch (E $e) {throw df_le($e);}
	return $result;
}