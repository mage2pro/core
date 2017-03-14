<?php
namespace Df\Payment\W;
use Df\Payment\W\Reader as R;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Df\Payment\W\Exception\Critical;
use Df\Payment\W\Exception\Ignored;
/**
 * 2017-01-02
 * @see \Df\StripeClone\W\F
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
	 * @used-by handler()
	 * @used-by \Df\PaypalClone\TM::responses()
	 * @return Event
	 * @throws Critical|Ignored
	 */
	final function event() {return dfc($this, function() {return Event::i(
		$this->c(self::$EVENT), $this->_r
	);});}

	/**
	 * 2017-01-02
	 * @used-by \Df\Payment\W\Action::execute()
	 * @return Handler
	 */
	final function handler() {return df_new($this->c(self::$HANDLER), $this->event());}

	/**
	 * 2017-03-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\W\F::suf()
	 * @return R
	 */
	protected function r() {return $this->_r;}

	/**
	 * 2017-03-13
	 * @used-by c()
	 * @see \Df\StripeClone\W\F::suf()
	 * @see \Dfe\AllPay\W\F::suf()
	 * @param string $a
	 * @param string|null $t
	 * @return string|string[]|null
	 */
	protected function suf($a, $t) {return df_cc_class_uc(
		$a, df_clean(df_explode_multiple(['.', '_'], $t))
	);}

	/**
	 * 2017-01-07
	 * 2017-03-11
	 * Важно, чтобы $m было именно классом Method.
	 * Это позволяет при применении @uses df_con_hier()
	 * гарантированно (!) проходить по всей иерархии модулей, например: Omise => StripeClone => Payment.
	 * Это решает проблему https://github.com/mage2pro/core/blob/2.1.7/Payment/Action/Webhook.php#L30-L42
	 * @param string|object $m
	 * @param array(string => mixed)|null $req [optional]
	 */
	private function __construct($m, $req = null) {
		$this->_m = $m;
		$this->_r = R::i($m, $req);
		/** @var S $s */
		$s = S::conventionB($this->_m);
		$s->init();
	}

	/**
	 * 2017-03-10
	 * 2016-03-18
	 * https://stripe.com/docs/api#event_object-type
	 * Пример события с обоими разделителями: «charge.dispute.funds_reinstated»
	 * 2017-03-13
	 * Имеется 2 пути спуска по иерархии:
	 * 1) спуск по иерархии наследования.
	 * 2) спуск по составному типу события
	 * По умолчанию реализован только первый путь.
	 * Вы можете реализовать второй путь перекрытием suf(): @see \Dfe\AllPay\W\F::suf()
	 * @param string $aspect
	 * @param bool $critical [optional]
	 * @return string
	 * @throws Critical|Ignored
	 */
	private function c($aspect, $critical = false) {
		/** @var R $r */
		$r = $this->_r;
		/** @var string $suf */
		/** @var string|null $t */
		$suf = df_cc_class_uc('W', $this->suf($aspect, $t = $r->t()));
		/** @var string $m */
		/** @var string|null $result */
		if (!($result = df_con_hier_suf($m = $this->_m, $suf, false))) {
			/** @var string $class */
			throw !$critical
				? new Ignored($m, $r, $t)
				: new Critical($m, $r, "The required class %s is %s.",
					$class = df_cc_class(df_module_name_c($m), $suf)
					,df_class_exists($class) ? 'abstract' : 'absent'
				)
			;
		}
		return $result;
	}

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
	 * @used-by __construct()
	 * @var string
	 */
	private $_m;

	/**
	 * 2017-03-13
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\PaypalClone\TM::responses()
	 * @param string|object $m
	 * @param array(string => mixed)|null $req [optional]
	 * @return self
	 * @throws Critical|Ignored
	 */
	static function s($m, $req = null) {return dfcf(function($m, $req = null) {
		/** @var string $c */
		$c = df_con_hier($m, self::class);
		return new $c($m, $req);
	}, [self::m($m), $req]);}

	/**
	 * 2017-03-13
	 * @used-by event()
	 * @used-by s()
	 * @param string|object $m
	 * @return string
	 */
	private static function m($m) {return $m instanceof M ? df_cts($m) : dfp_method_c($m);}

	/**
	 * 2017-03-13
	 * @used-by event()
	 * @used-by \Df\StripeClone\W\F::suf()
	 * @used-by \Dfe\AllPay\W\F::suf()
	 * @var string
	 */
	protected static $EVENT = 'Event';

	/**
	 * 2017-03-13
	 * @used-by handler()
	 * @var string
	 */
	protected static $HANDLER = 'Handler';
}