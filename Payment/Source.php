<?php
namespace Df\Payment;
// 2017-04-10
/** @see \Df\PaypalClone\Source\OptionsLocation */
abstract class Source extends \Df\Config\Source {
	/**
	 * 2017-04-10
	 * @used-by \Df\PaypalClone\Source\OptionsLocation::map()
	 * @return string
	 */
	final protected function titleB() {return $this->sibling('title_backend');}
}