<?php
namespace Df\Core;
// 2017-08-30
/** @see \Df\Payment\Method */
interface ICached {
	/**
	 * 2017-08-30
	 * @used-by \Df\Core\RAM::set()
	 * @see \Df\Payment\Method::tags()
	 * @return string[]
	 */
	function tags();
}