<?php
namespace Df\Payment\W;
use Df\Payment\IMA;
use Df\Payment\Method as M;
use Df\Payment\W\Exception\NotForUs;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use \Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2016-07-09 Портировал из Российской сборки Magento.
 * @see \Dfe\GingerPaymentsBase\W\Handler
 * @see \Df\PaypalClone\W\Handler
 * @see \Dfe\Dragonpay\W\Handler
 * @see \Dfe\Omise\W\Handler\Charge\Capture
 * @see \Dfe\Omise\W\Handler\Charge\Complete
 * @see \Dfe\Omise\W\Handler\Refund\Create
 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded
 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded
 * @see \Dfe\Qiwi\W\Handler
 * @see \Dfe\Stripe\W\Handler\Charge\Captured
 * @see \Dfe\Stripe\W\Handler\Charge\Refunded
 * @see \Dfe\Stripe\W\Handler\Source
 * @see \Dfe\TBCBank\W\Handler
 */
abstract class Handler implements IMA {
	/**
	 * 2017-01-06
	 * 1) Stripe-подобные платёжные системы, в отличие от PayPal-подобных,
	 * отличаются богатством типов оповещений.
	 * 2) PayPal-подобные платёжные системы присылают, как правило, только один тип оповещений:
	 * оповещение о факте успешности (или неуспешности) оплаты покупателем заказа.
	 * 3) У Stripe-подобных платёжных систем типов оповещений множество, причём они порой образуют целые иерархии.
	 * Например, у Stripe оповещения об изменении статуса платежа объединяются в группу «charge»,
	 * которой принадлежат такие типы оповещений, как «charge.captured» и «charge.refunded».
	 * 4) Разные Stripe-подобные платёжные системы обладают схожими типами платежей.
	 * Пример — те же самые «charge.captured» и «charge.refunded».
	 * По этой причине разумно выделять не только общие черты,
	 * свойственные конкретной Stripe-подобной платёжной системе
	 * и отличащие её от других Stripe-подобных платёжных систем,
	 * но и общие черты типов платежей: обработка того же «charge.captured»
	 * должна иметь общую реализацию для всех Stripe-подобных платёжных модулей.
	 * 5) Для реализации такой системы из двух параллельных иерархий
	 * я вынес в стратегию иерархию обработчиков разных типов платежей.
	 * @used-by self::handle()
	 * @see \Dfe\GingerPaymentsBase\W\Handler::strategyC()
	 * @see \Df\PaypalClone\W\Handler::strategyC()
	 * @see \Dfe\Dragonpay\W\Handler::strategyC()
	 * @see \Dfe\Omise\W\Handler\Charge\Capture::strategyC()
	 * @see \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded::strategyC()
	 * @see \Dfe\Qiwi\W\Handler::strategyC()
	 * @see \Dfe\Stripe\W\Handler\Charge\Captured::strategyC()
	 * @see \Dfe\Stripe\W\Handler\Charge\Refunded::strategyC()
	 * @see \Dfe\Stripe\W\Handler\Source::strategyC()
	 * @see \Dfe\TBCBank\W\Handler::strategyC()
	 */
	abstract protected function strategyC():string;

	/**
	 * 2017-01-01
	 * @used-by \Df\Payment\W\F::handler()
	 */
	final function __construct(F $f, Event $e) {$this->_e = $e; $this->_f = $f; $this->_nav = $f->nav();}

	/**
	 * 2017-03-15
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\GingerPaymentsBase\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Strategy::e()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @used-by \Dfe\Qiwi\W\Handler::amount()
	 * @used-by \Dfe\Qiwi\W\Handler::eTransId()
	 * @used-by \Dfe\Qiwi\W\Handler::strategyC()
	 */
	function e():Event {return $this->_e;}

	/**
	 * 2016-07-04
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	final function handle():void {
		try {
			if ($this->m()->s()->log()) {
				$this->log();
			}
			$this->_e->validate();
			if ($c = $this->strategyC()) { /** @var string $c */
				Strategy::handle($c, $this);
			}
		}
		/** 2017-09-15 @uses NotForUs is thrown from @see \Df\Payment\W\Nav::p() */
		catch (NotForUs $e) {
			$this->log();
			$this->responder()->setNotForUs(df_xts($e));
		}
		# 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
		catch (Th $th) {
			$this->log();
			$this->log($th);
			# 2016-07-15
			# Раньше тут стояло
			#	if ($this->_order) {
			#		$this->_order->cancel();
			#		$this->_order->save();
			#	}
			# На самом деле, исключительная ситуация свидетельствует о сбое в программе,
			# либо о некорректном запросе якобы от платёжного сервера (хакерской попытке, например),
			# поэтому отменять заказ тут неразумно.
			# В случае сбоя платёжная система будет присылать повторные оповещения —
			# вот пусть и присылает, авось мы к тому времени уже починим программу,
			# если поломка была на нашей строне.
			$this->responder()->setError($th);
		}
	}

	/**
	 * 2016-08-14
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by self::handle()
	 * @used-by \Dfe\GingerPaymentsBase\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @used-by \Df\Payment\W\Strategy::m()
	 */
	function m():M {return dfc($this, function() {return dfpm($this->op());});}

	/**
	 * 2017-03-15  
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 */
	final function nav():Nav {return $this->_nav;}

	/**
	 * 2016-07-10
	 * 2017-01-06 Аналогично можно получить результат и из транзакции: $this->tParent()->getOrder()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy::o()
	 */
	final function o():O {return df_order($this->op());}

	/**
	 * 2016-07-10
	 * 2017-01-04
	 * @used-by self::m()
	 * @used-by self::o()
	 * @used-by self::_handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy::op()
	 */
	final function op():OP {return $this->_nav->op();}

	/**
	 * 2017-11-18
	 * @used-by self::handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 */
	final function responder():Responder {return $this->_f->responder();}

	/**
	 * 2017-01-01
	 * @used-by self::log()
	 * @used-by \Dfe\Qiwi\W\Handler::amount()
	 * @return mixed|null
	 */
	final protected function r(string $k = '') {return $this->_e->r($k);}

	/**
	 * 2016-12-26
	 * 2017-03-30 Используем @uses dfc(), чтобы метод игнорировал повторный вызов с прежним параметром.
	 * 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	 * @used-by self::handle()
	 */
	private function log(Th $th = null):void {dfc($this, function(Th $th = null) {
		/**
		 * 2017-03-30
		 * Намеренно не используем здесь @see self::m(),
		 * потому что этот метод работает через @see self::op(),
		 * а этот метод может падать: например, если транзакция не найдена.
		 */
		$m = $this->_f->m(); /** @var M $m */
		$title = dfpm_title($m); /** @var string $title */
		/** @var Th|string $v */ /** @var string|null $suffix */
		if ($th) {
			# 2020-03-02, 2022-10-31
			# 1) Symmetric array destructuring requires PHP ≥ 7.1:
			#		[$a, $b] = [1, 2];
			# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
			# We should support PHP 7.0.
			# https://3v4l.org/3O92j
			# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
			# https://stackoverflow.com/a/28233499
			# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
			[$v, $suffix] = [$th, 'exception'];
			df_log($th, $m);
		}
		else {
			$ev = $this->_e; /** @var Event $ev */
			$v = df_ccc(': ', "[{$title}] {$ev->tl()}", $ev->statusT());
			$suffix = df_es($t = $ev->t()) ? '' : df_fs_name($t); /** @var string $t $suffix */
		}
		df_sentry_m($m)->user(['id' => $title]);
		dfp_sentry_tags($m);
		# 2023-07-25
		# "Change the 3rd argument of `df_sentry` from `$context` to `$extra`": https://github.com/mage2pro/core/issues/249
		df_sentry($m, $v, $d = $this->r()); /** @var mixed $d */
		df_log_l($m, $d, $suffix);
	}, [$th]);}

	/**
	 * 2017-03-10
	 * @used-by self::__construct()
	 * @used-by self::event()
	 * @var Event
	 */
	private $_e;

	/**
	 * 2017-03-30
	 * @used-by self::__construct()
	 * @used-by self::log()
	 * @used-by self::responder()
	 * @var F
	 */
	private $_f;

	/**
	 * 2017-03-15
	 * @used-by self::__construct()
	 * @used-by self::nav()
	 * @used-by self::op()
	 * @var Nav
	 */
	private $_nav;
}