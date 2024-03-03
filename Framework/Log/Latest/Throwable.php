<?php
namespace Df\Framework\Log\Latest;
use \Throwable As T;
# 2024-03-04
final class Throwable implements O {
	/**
	 * 2024-03-04
	 * @used-by \Df\Framework\Log\Latest::o()
	 */
	function __construct(T $t) {$this->_t = $t;}

	/**
	 * 2024-03-04
	 * @override
	 * @see O::id()
	 * @used-by \Df\Framework\Log\Latest::register()
	 * @used-by \Df\Framework\Log\Latest::registered()
	 */
	function id():string {return $this->_t->getMessage();}

	/**
	 * 2024-03-04
	 * @used-by self::__construct()
	 * @used-by self::id()
	 * @var T
	 */
	private $_t;
}