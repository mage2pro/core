<?php
namespace Df\Payment\W;
use Df\Core\Exception as DFE;
use Df\Framework\Controller\Result\Text;
use Df\Payment\IMA;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Df\Payment\W\Exception\NotForUs;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
/**
 * 2016-07-09 Портировал из Российской сборки Magento.
 * @see \Df\PaypalClone\W\Handler
 * @see \Df\StripeClone\W\Handler
 * @see \Df\GingerPaymentsBase\W\Handler
 * @see \Dfe\Stripe\W\Handler\Charge\Captured
 * @see \Dfe\Stripe\W\Handler\Charge\Refunded
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
	 * @see \Dfe\Omise\W\Handler\Charge\Capture::strategyC()
	 * @see \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded::strategyC()
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
	final function __construct(F $f, Event $e) {$this->_e = $e; $this->_f = $f; $this->_nav = $f->nav();
	}

	/**
	 * 2017-03-15
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy::e()
	 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @used-by \Dfe\Robokassa\W\Handler::result()
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
			$this->_e->validate();
			/** @var string|null $c */
			if ($c = $this->strategyC()) {
				Strategy::handle($c, $this);
			}
		}
		catch (NotForUs $e) {
			$this->log();
			$this->resultSet($this->resultNotForUs(df_ets($e)));
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
			//если поломка была на нашей строне.
			$this->resultSet(static::resultError($e));
		}
		/**
		 * 2017-04-13
		 * Алгоритм должен быть именно таким, потому что поле @uses $_result может быть уже инициализировано
		 * диагностическим сообщением о сбое, а метод @uses result() может перекрываться потомками:
		 * @see \Dfe\AllPay\W\Handler::result()
		 * @see \Dfe\IPay88\W\Handler::result()
		 * которые просто возвращают код успешной обработки.
		 */
		return !is_null($this->_result) ? $this->_result : $this->result();
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
	 * 2017-01-07
	 * @used-by handle()
	 * @used-by \Df\Payment\W\Strategy::resultSet()
	 * @param Result|Phrase|string|null $v
	 */
	final function resultSet($v) {$this->_result =
		($v = is_string($v) ?  __($v) : $v) instanceof Phrase ? Text::i($v) : $v
	;}

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @see \Dfe\AllPay\W\Handler::result()
	 * @see \Dfe\Dragonpay\W\Handler::result()
	 * @see \Dfe\IPay88\W\Handler::result()
	 * @see \Dfe\Robokassa\W\Handler::result()
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
			$v = df_ccc(': ', "[{$title}] {$ev->tl()}", $ev->logTitleSuffix());
			$suffix = is_null($t = $ev->t()) ? null : df_fs_name($t); /** @var string|null $t $suffix */
		}
		df_sentry_m($m)->user_context(['id' => $title]);
		dfp_sentry_tags($m);
		/** @var string $data */
		df_sentry($m, $v, ['extra' => ['Payment Data' => $data = df_json_encode($this->r())]]);
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

	/**
	 * 2017-01-07
	 * @used-by handle()
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