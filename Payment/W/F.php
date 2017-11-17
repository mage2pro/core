<?php
namespace Df\Payment\W;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Df\Payment\W\Exception\Critical;
use Df\Payment\W\Exception\Ignored;
use Df\Payment\W\Reader as R;
/**
 * 2017-01-02
 * @see \Dfe\AllPay\W\F
 * 2017-03-14
 * Окончание «Factory» для подобных классов использовать нельзя:
 * оно зарезервировано для фабрик Magento 2
 * и в нашем случае его применение приведёт к сбою:
 * «Source class "\Dfe\Stripe\W" for "Dfe\Stripe\W\Factory" generation does not exist.»
 */
class F {
	/**
	 * 2017-03-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by handler()
	 * @used-by \Df\Payment\TM::responses()
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Payment\W\Responder::e()
	 * @return Event
	 * @throws Critical|Ignored
	 */
	function e() {return $this->aspect(Event::class, $this->_r);}

	/**
	 * 2017-03-13
	 * @used-by \Df\Payment\W\Action::execute()
	 * @return Handler
	 */
	final function handler() {return $this->aspect(Handler::class, $this, $this->e());}

	/**
	 * 2017-03-30
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @return M
	 */
	final function m() {return $this->_m;}

	/**
	 * 2017-03-15
	 * @used-by \Df\Payment\W\Handler::__construct
	 * @return Nav
	 */
	final function nav() {return $this->aspect(Nav::class, $this->e());}

	/**
	 * 2017-03-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\W\F::sufEvent()
	 * @used-by \Dfe\AllPay\W\F::sufNav()
	 * @return R
	 */
	protected function r() {return $this->_r;}

	/**
	 * 2017-09-12
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @return Responder
	 */
	final function responder() {return $this->aspect(Responder::class, $this);}

	/**
	 * 2017-01-07
	 * 2017-03-11
	 * Важно, чтобы $m было именно классом Method.
	 * Это позволяет при применении @uses df_con_hier()
	 * гарантированно (!) проходить по всей иерархии модулей, например: Omise => StripeClone => Payment.
	 * Это решает проблему https://github.com/mage2pro/core/blob/2.1.7/Payment/Action/Webhook.php#L30-L42
	 * 2017-03-17
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @param M $m
	 * @param array(string => mixed)|null $req [optional]
	 */
	private function __construct(M $m, $req = null) {
		$this->_m = $m;
		$this->_r = df_new(df_con_hier($m, R::class), $m, $req);
		$this->_m->s()->init();
	}

	/**
	 * 2017-03-15
	 * @used-by event()
	 * @used-by handler()
	 * @param string $base
	 * @param mixed[] ...$a  
	 * @return object
	 * @throws Critical|Ignored
	 */
	private function aspect($base, ...$a) {return dfc($this, function($base, ...$a) {return
		df_newa($this->c(df_class_l($base)), $base, ...$a)
	;}, func_get_args());}

	/**
	 * 2017-03-10
	 * 2016-03-18
	 * https://stripe.com/docs/api#event_object-type
	 * Пример события с обоими разделителями: «charge.dispute.funds_reinstated»
	 * 2017-03-13
	 * Имеется 2 пути спуска по иерархии:
	 * 1) спуск по иерархии наследования.
	 * 2) спуск по составному типу события
	 * 2017-03-15
	 * Отныне мы реализуем ОБА пути спуска.
	 * 1) @see trySuf()
	 * 2) смотрите цикл while в методе c().
	 * @used-by aspect()
	 * @param string $a
	 * @param bool $critical [optional]
	 * @return string
	 * @throws Critical|Ignored
	 */
	private function c($a, $critical = false) {
		$r = $this->_r; /** @var R $r */
		$m = $this->_m; /** @var M $m */
		$t = $r->t(); /** @var string|null $t */
		/**
		 * 2017-03-16
		 * @uses \Dfe\AllPay\W\F::sufEvent()
		 * @uses \Dfe\AllPay\W\F::sufNav()
		 * @var string $f
		 * @var string $result
		 */
		if (!($result = !is_callable([$this, $f = "suf$a"]) ? null : $this->try_($a, $this->$f($t)))) {
			// 2017-03-20 Сначала проходим по иерархии суффиксов, и лишь затем — по иерархии наследования.
			$result = $this->tryTA($a, df_clean(df_explode_multiple(['.', '_'], $t)));
		}
		return $result ?: ($this->try_($a) ?: df_error(!$critical
			? new Ignored($m, $r, $t)
			: new Critical($m, $r, "The required class %s is %s.",
				$class = df_cc_class(df_module_name_c($m), 'W', $a)
				,df_class_exists($class) ? 'abstract' : 'absent'
			)
		));
	}

	/**
	 * 2017-03-15 Cпуск по иерархии наследования.
	 * @used-by c()
	 * @param array(string|null) ...$s
	 * @return string|null
	 */
	private function try_(...$s) {return df_con_hier_suf($this->_m, df_cc_class_uc('W', $s), false);}

	/**
	 * 2017-03-15 Сначала проходит по иерархии суффиксов, и лишь затем — по иерархии наследования.
	 * @used-by c()
	 * @param string $a
	 * @param string[] $ta
	 * @return string|null
	 */
	private function tryTA($a, array $ta) {return df_con_hier_suf_ta($this->_m, ['W', $a], $ta, false);}

	/**
	 * 2017-03-13
	 * @used-by __construct()
	 * @used-by c()
	 * @used-by event()
	 * @var R
	 */
	private $_r;

	/**
	 * 2017-01-07
	 * 2017-03-17
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @used-by __construct()
	 * @used-by c()
	 * @used-by m()
	 * @used-by try_()
	 * @used-by tryTA()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-03-13
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Payment\TM::responses()
	 * @used-by \Dfe\SecurePay\Signer\Response::values()
	 * @param string|object $m
	 * @param array(string => mixed)|null $req [optional]
	 * @return self
	 * @throws Critical|Ignored
	 */
	final static function s($m, $req = null) {return dfcf(function(M $m, $req = null) {
		$c = df_con_hier($m, self::class); /** @var string $c */
		return new $c($m, $req);
	}, [dfpm($m), $req]);}
}