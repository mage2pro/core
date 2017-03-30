<?php
namespace Df\Payment\W;
use Df\Core\Exception as DFE;
use Df\Framework\Controller\Result\Text;
use Df\Payment\IMA;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Df\Payment\W\Exception\Critical;
use Df\Payment\W\Exception\NotForUs;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
/**
 * 2016-07-09
 * Портировал из Российской сборки Magento.
 * @see \Df\PaypalClone\W\Handler
 * @see \Df\StripeClone\W\Handler
 */
abstract class Handler implements IMA {
	/**
	 * 2017-01-01
	 * @used-by handle()
	 * @see \Df\PaypalClone\W\Handler::_handle()
	 * @see \Df\StripeClone\W\Handler::_handle()
	 * @return void
	 */
	abstract protected function _handle();

	/**
	 * 2017-01-01
	 * @used-by \Df\Payment\W\F::handler()
	 * @param F $f
	 * @param Event $e
	 */
	final function __construct(F $f, Event $e) {$this->_e = $e; $this->_nav = $f->nav();}

	/**
	 * 2017-03-15
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\PaypalClone\W\Handler::_handle()
	 * @used-by \Df\StripeClone\W\Strategy::e()
	 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @return Event
	 */
	function e() {return $this->_e;}

	/**
	 * 2016-07-04
	 * @override
	 * @return Result
	 */
	final function handle() {
		try {
			if ($this->m()->s()->log()) {
				$this->log();
			}
			$this->validate();
			$this->_handle();
		}
		catch (NotForUs $e) {
			$this->resultSet($this->resultNotForUs(df_ets($e)));
		}
		catch (\Exception $e) {
			$this->log($e);
			// 2016-07-15
			// Раньше тут стояло
			//	if ($this->_order) {
			//		$this->_order->cancel();
			//		$this->_order->save();
			//	}
			// На самом деле, исключительная ситуация свидетельствует о сбое в программе,
			// либо о некорректном запросе якобы от платёжного сервера (хакерской попытке, например),
			// поэтому отменять заказ тут неразумно.
			// В случае сбоя платёжная система будет присылать повторные оповещения —
			// вот пусть и присылает, авось мы к тому времени уже починим программу,
			//если поломка была на нашей строне.
			$this->resultSet(static::resultError($e));
		}
		return $this->result();
	}

	/**
	 * 2016-08-14
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by handle()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @used-by \Df\PaypalClone\Signer::_sign()
	 * @used-by \Df\StripeClone\W\Strategy::m()
	 * @return M
	 */
	function m() {return dfc($this, function() {return dfpm($this->op());});}

	/**
	 * 2017-03-15  
	 * @used-by \Df\StripeClone\W\Strategy\Refund::_handle()
	 * @return Nav
	 */
	final function nav() {return $this->_nav;}

	/**
	 * 2016-07-10
	 * 2017-01-06
	 * Аналогично можно получить результат и из транзакции: $this->tParent()->getOrder()
	 * @used-by \Df\PaypalClone\W\Handler::_handle()
	 * @used-by \Df\StripeClone\W\Strategy::o()
	 * @return O
	 */
	final function o() {return df_order($this->op());}

	/**
	 * 2016-07-10
	 * 2017-01-04
	 * @used-by m()
	 * @used-by o()
	 * @used-by _handle()
	 * @used-by \Df\PaypalClone\W\Handler::_handle()
	 * @used-by \Df\StripeClone\W\Strategy::op()
	 * @return OP
	 */
	final function op() {return $this->_nav->op();}

	/**
	 * 2017-01-01
	 * @used-by cv()
	 * @used-by log()
	 * @used-by \Dfe\AllPay\Block\Info\ATM::paymentId()
	 * @used-by \Dfe\AllPay\W\Event\BankCard::numPayments()
	 * @used-by \Dfe\AllPay\W\Event\Offline::expiration()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final function r($k = null, $d = null) {return $this->_e->r($k, $d);}

	/**
	 * 2017-01-12
	 * @used-by \Df\StripeClone\W\Handler::ro()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed
	 * @throws Critical
	 */
	final function rr($k = null, $d = null) {return $this->_e->rr($k, $d);}

	/**
	 * 2017-01-07
	 * @used-by handle()
	 * @used-by \Df\StripeClone\W\Strategy::resultSet()
	 * @param Result|Phrase|string|null $v
	 * @return void
	 */
	final function resultSet($v) {$this->_result =
		($v = is_string($v) ?  __($v) : $v) instanceof Phrase ? Text::i($v) : $v
	;}

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @see \Dfe\AllPay\W\Handler::result()
	 * @return Result
	 */
	protected function result() {return !is_null($this->_result) ? $this->_result : Text::i('success');}

	/**
	 * 2017-01-04
	 * @used-by handle()
	 * @see \Dfe\AllPay\W\Handler::resultNotForUs()
	 * @param string|null $message [optional]
	 * @return Result
	 */
	protected function resultNotForUs($message) {return Text::i($message);}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	final protected function store() {return $this->o()->getStore();}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by handle()
	 * @see \Df\PaypalClone\W\Handler::validate()
	 * @return void
	 * @throws \Exception
	 */
	protected function validate() {}

	/**
	 * 2016-12-26
	 * @used-by handle()
	 * @used-by resultError()
	 * @param \Exception|null $e [optional]
	 * @return void
	 */
	private function log(\Exception $e = null) {
		/** @var M $m */
		$m = $this->m();
		/** @var string $title */
		$title = dfpm_title($m);
		/** @var \Exception|string $v */
		/** @var string|null $suffix */
		if ($e) {
			list($v, $suffix) = [$e, 'exception'];
			df_log_l($e);
		}
		else {
			/** @var Event $ev */
			$ev = $this->_e;
			$v = df_ccc(': ', "[{$title}] {$ev->tl()}", $ev->logTitleSuffix());
			/** @var string|null $t $suffix */
			$suffix = is_null($t = $ev->t()) ? null : df_fs_name($t);
		}
		df_sentry_m($m)->user_context(['id' => $title]);
		dfp_sentry_tags($this->m());
		/** @var string $data */
		df_sentry($m, $v, ['extra' => ['Payment Data' => $data = df_json_encode_pretty($this->r())]]);
		dfp_log_l($m, $data, $suffix);
	}

	/**
	 * 2017-03-10
	 * @used-by __construct()
	 * @used-by event()
	 * @var Event
	 */
	private $_e;

	/**
	 * 2017-03-15
	 * @used-by __construct()
	 * @used-by nav()
	 * @used-by op()
	 * @var Nav
	 */
	private $_nav;

	/**
	 * 2017-01-07
	 * @used-by result()
	 * @used-by resultSet()
	 * @var Result
	 */
	private $_result;

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @used-by \Df\Payment\W\Action::error()
	 * @see \Dfe\AllPay\W\Handler::resultError()
	 * @param \Exception $e
	 * @return Text
	 */
	static function resultError(\Exception $e) {return Text::i(df_lets($e))->setHttpResponseCode(500);}
}