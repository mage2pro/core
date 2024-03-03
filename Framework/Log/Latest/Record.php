<?php
namespace Df\Framework\Log\Latest;
use Df\Framework\Log\Record as R;
use Monolog\Logger as L;
# 2024-03-04
final class Record implements O {
	/**
	 * 2024-03-04
	 * @used-by \Df\Framework\Log\Latest::o()
	 */
	function __construct(R $r) {$this->_r = $r;}

	/**
	 * 2024-03-04
	 * @override
	 * @see O::id()
	 * @used-by \Df\Framework\Log\Latest::register()
	 * @used-by \Df\Framework\Log\Latest::registered()
	 */
	function id():string {return L::ERROR !== $this->_r->level() ? '' : $this->_r->msg();}

	/**
	 * 2024-03-04
	 * @used-by self::__construct()
	 * @used-by self::id()
	 * @var R
	 */
	private $_r;
}