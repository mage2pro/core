<?php
namespace Df\Sentry;
final class TransactionStack {
	/**
	 * 2020-06-27
	 * @used-by \Df\Sentry\Client::__construct()
	 */
	function __construct() {$this->stack = [];}

	/**
	 * 2022-11-11
	 * @used-by \Df\Sentry\Client::capture()
	 * @return string|null
	 */
	function peek() {
		$len = count($this->stack);
		if ($len === 0) {
			return null;
		}
		return $this->stack[$len - 1];
	}

	/**
	 * 2022-11-11
	 * @used-by \Df\Sentry\Client::__construct()
	 */
	function push(string $c):void {$this->stack[] = $c;}

	/**
	 * 2023-09-03
	 * «Creation of dynamic property Justuno\Core\Sentry\TransactionStack::$stack is deprecated
	 * in vendor/justuno.com/core/Sentry/TransactionStack.php on line 8»: https://github.com/justuno-com/core/issues/411
	 * @used-by self::__construct()
	 * @used-by self::peek()
	 * @used-by self::push()
	 * @var string[]
	 */
	private $stack;
}