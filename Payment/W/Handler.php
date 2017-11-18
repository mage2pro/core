<?php
namespace Df\Payment\W;
use Df\Core\Exception as DFE;
use Df\Payment\IMA;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Df\Payment\W\Exception\NotForUs;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
/**
 * 2016-07-09 Портировал из Российской сборки Magento.
 * @see \Df\GingerPaymentsBase\W\Handler
 * @see \Df\PaypalClone\W\Handler
 * @see \Dfe\Omise\W\Handler\Charge\Capture
 * @see \Dfe\Omise\W\Handler\Charge\Complete
 * @see \Dfe\Omise\W\Handler\Refund\Create
 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded
 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded
 * @see \Dfe\Qiwi\W\Handler
 * @see \Dfe\Stripe\W\Handler\Charge\Captured
 * @see \Dfe\Stripe\W\Handler\Charge\Refunded
 * @see \Dfe\Stripe\W\Handler\Source
 */
abstract class Handler implements IMA {
	/**
	 * 2017-01-06
	 * Stripe-подобные платёжные системы, в отличие от PayPal-подобных,
	 * отличаются богатством типов оповещений.
	 *
	 * PayPal-подобные платёжные системы присылают, как правило, только один тип оповещений:
	 * оповещение о факте успешности (или неуспешности) оплаты покупателем заказа.
	 *
	 * У Stripe-подобных платёжных систем типов оповещений множество,
	 * причём они порой образуют целые иерархии.
	 * Например, у Stripe оповещения об изменении статуса платежа объединяются в группу «charge»,
	 * которой принадлежат такие типы оповещений, как «charge.captured» и «charge.refunded».
	 *
	 * Разные Stripe-подобные платёжные системы обладают схожими типами платежей.
	 * Пример — те же самые «charge.captured» и «charge.refunded».
	 * По этой причине разумно выделять не только общие черты,
	 * свойственные конкретной Stripe-подобной платёжной системе
	 * и отличащие её от других Stripe-подобных платёжных систем,
	 * но и общие черты типов платежей: обработка того же «charge.captured»
	 * должна иметь общую реализацию для всех Stripe-подобных платёжных модулей.
	 *
	 * Для реализации такой системы из двух параллельных иерархий
	 * я вынес в стратегию иерархию обработчиков разных типов платежей.
	 * 
	 * @used-by handle()
	 * @see \Df\GingerPaymentsBase\W\Handler::strategyC()
	 * @see \Df\PaypalClone\W\Handler::strategyC()
	 * @see \Dfe\Omise\W\Handler\Charge\Capture::strategyC()
	 * @see \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded::strategyC()
	 * @see \Dfe\Qiwi\W\Handler::strategyC()
	 * @see \Dfe\Stripe\W\Handler\Charge\Captured::strategyC()
	 * @see \Dfe\Stripe\W\Handler\Charge\Refunded::strategyC()
	 * @return string|null
	 */
	abstract protected function strategyC();

	/**
	 * 2017-01-01
	 * @used-by \Df\Payment\W\F::handler()
	 * @param F $f
	 * @param Event $e
	 */
	final function __construct(F $f, Event $e) {$this->_e = $e; $this->_f = $f; $this->_nav = $f->nav();}

	/**
	 * 2017-03-15
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Strategy::e()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @used-by \Dfe\Qiwi\W\Handler::amount()
	 * @used-by \Dfe\Qiwi\W\Handler::eTransId()
	 * @used-by \Dfe\Qiwi\W\Handler::strategyC()
	 * @return Event
	 */
	function e() {return $this->_e;}

	/**
	 * 2016-07-04
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	final function handle() {
		try {
			if ($this->m()->s()->log()) {
				$this->log();
			}
			$this->_e->validate();
			if ($c = $this->strategyC()) { /** @var string|null $c */
				Strategy::handle($c, $this);
			}
		}
		/** 2017-09-15 @uses NotForUs is thrown from @see \Df\Payment\W\Nav::p() */
		catch (NotForUs $e) {
			$this->log();
			$this->responder()->setNotForUs(df_ets($e));
		}
		catch (\Exception $e) {
			$this->log();
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
			// если поломка была на нашей строне.
			$this->responder()->setError($e);
		}
	}

	/**
	 * 2016-08-14
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by handle()
	 * @used-by \Df\GingerPaymentsBase\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @used-by \Df\Payment\W\Strategy::m()
	 * @return M
	 */
	function m() {return dfc($this, function() {return dfpm($this->op());});}

	/**
	 * 2017-03-15  
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 * @return Nav
	 */
	final function nav() {return $this->_nav;}

	/**
	 * 2016-07-10
	 * 2017-01-06
	 * Аналогично можно получить результат и из транзакции: $this->tParent()->getOrder()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy::o()
	 * @return O
	 */
	final function o() {return df_order($this->op());}

	/**
	 * 2016-07-10
	 * 2017-01-04
	 * @used-by m()
	 * @used-by o()
	 * @used-by _handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy::op()
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
	 * 2017-11-18
	 * @used-by handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @return Responder
	 */
	final function responder() {return $this->_f->responder();}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	final protected function store() {return $this->o()->getStore();}

	/**
	 * 2016-12-26
	 * 2017-03-30 Используем @uses dfc(), чтобы метод игнорировал повторный вызов с прежним параметром.
	 * @used-by handle()
	 * @param \Exception|null $e [optional]
	 */
	private function log(\Exception $e = null) {dfc($this, function(\Exception $e = null) {
		/**
		 * 2017-03-30
		 * Намеренно не используем здесь не @see m(), потому что этот метод работает через @see op(),
		 * а этот метод может падать: например, если транзакция не найдена.
		 */
		$m = $this->_f->m(); /** @var M $m */
		$title = dfpm_title($m); /** @var string $title */
		/** @var \Exception|string $v */ /** @var string|null $suffix */
		if ($e) {
			list($v, $suffix) = [$e, 'exception'];
			df_log_e($e);
		}
		else {
			$ev = $this->_e; /** @var Event $ev */
			$v = df_ccc(': ', "[{$title}] {$ev->tl()}", $ev->statusT());
			$suffix = is_null($t = $ev->t()) ? null : df_fs_name($t); /** @var string|null $t $suffix */
		}
		df_sentry_m($m)->user_context(['id' => $title]);
		dfp_sentry_tags($m);
		df_sentry($m, $v, ['extra' => ($data = $this->r())]); /** @var string $data */
		df_log_l($m, $data, $suffix);
	}, [$e]);}

	/**
	 * 2017-03-10
	 * @used-by __construct()
	 * @used-by event()
	 * @var Event
	 */
	private $_e;

	/**
	 * 2017-03-30
	 * @used-by __construct()
	 * @used-by responder()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @var F
	 */
	private $_f;

	/**
	 * 2017-03-15
	 * @used-by __construct()
	 * @used-by nav()
	 * @used-by op()
	 * @var Nav
	 */
	private $_nav;
}