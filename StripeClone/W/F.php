<?php
namespace Df\StripeClone\W;
use Df\Payment\W\Event;
// 2017-03-14
final class F extends \Df\Payment\W\F {
	/**
	 * 2017-03-14
	 * @override
	 * @see \Df\Payment\W\F::suf()
	 * @used-by \Df\Payment\W\F::c()
	 * @param string $a
	 * @param string|null $t
	 * @return string|string[]|null
	 */
	protected function suf($a, $t) {return
		self::$EVENT !== $a ? parent::suf($a, $t) : df_class_l(Event::class)
	;}
}