<?php
namespace Df\Payment;
# 2017-04-10
/** @see \Df\Payment\Source\Options\Location */
abstract class Source extends \Df\Config\Source {
	/**
	 * 2017-04-10
	 * @used-by \Df\Payment\Source\Options\Location::map()
	 */
	final protected function titleB():string {return $this->sibling('title_backend');}
}