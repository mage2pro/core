<?php
namespace Df\Qa\Message\Failure;
final class Error extends \Df\Qa\Message\Failure {
	/**
	 * 2015-04-04
	 * Обратите внимание, что статичные методы @uses type() и @uses info()
	 * мы намеренно вызываем нестатично ради синтаксиса {},
	 * и мы вправе это делать: http://3v4l.org/jro9u
	 * @override
	 * @see \Df\Qa\Message::main()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function main() {
		return
			"[{$this->type($asString = true)}] {$this->info('message')}"
			. "\nФайл: {$this->info('file')}"
			." \nСтрока: {$this->info('line')}"
		;
	}

	/**
	 * @override
	 * @see \Df\Qa\Message_Failure::stackLevel()
	 * @used-by \Df\Qa\Message_Failure::states()
	 * @return int
	 */
	protected function stackLevel() {return 13;}

	/**
	 * @see debug_backtrace() не работает в функции-обработчике
	 * @see register_shutdown_function()
	 * Однако @uses xdebug_get_function_stack() — работает.
	 * @override
	 * @see \Df\Qa\Message_Failure::trace()
	 * @used-by \Df\Qa\Message_Failure::states()
	 * @return array(array(string => string|int))
	 */
	protected function trace() {
		return self::xdebug() ? array_reverse(xdebug_get_function_stack()) : [];
	}

	/**
	 * @used-by isFatal()
	 * @used-by main()
	 * @param bool $asString [optional]
	 * @return int|string
	 */
	private static function type($asString = false) {
		/** @var int|string $result */
		$result = df_nat0(self::info('type'));
		return !$asString ? $result : dfa(self::map(), $result);
	}

	/**
	 * @used-by Df_Core_Boot::init1()
	 * @return void
	 */
	public static function check() {
		/**
		 * 2015-04-05
		 * Оборачиваем код в try..catch,
		 * чтобы не утратить сообщение о внутреннем сбое при асинхронном запросе.
		 */
		try {
			if (error_get_last() && self::isFatal()) {
				self::i()->log();
			}
		}
		catch (\Exception $e) {
			df_log(df_ets($e));
		}
	}

	/**
	 * @used-by df_throw_last_error()
	 * @return void
	 * @throws Exception
	 */
	public static function throwLast() {
		df_assert(error_get_last());
		df_error(self::i()->main());
	}

	/**
	 * @used-by check()
	 * @used-by throwLast()
	 * @return \Df\Qa\Message\Failure\Error
	 */
	private static function i() {return new self;}

	/**
	 * @used-by main()
	 * @used-by type()
	 * @param string $key
	 * @return string|int
	 */
	private static function info($key) {return dfa(error_get_last(), $key);}

	/**
	 * @used-by check()
	 * @return bool
	 * @return int[]
	 */
	private static function isFatal() {
		/** @var array(int => int) $map */
		static $map;
		if (!$map) {
			$map = [
				E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
			];
			// xDebug при E_RECOVERABLE_ERROR останавивает работу интерпретатора
			if (self::xdebug()) {
				$map[]= E_RECOVERABLE_ERROR;
			}
			$map = array_flip($map);
		}
		return isset($map[self::type()]);
	}

	/**
	 * @used-by type()
	 * @return array(int => string)
	 */
	private static function map() {
		/** @var array(int => string) $result */
		static $result;
		if (!$result) {
			$result = [
				E_ERROR => 'E_ERROR'
				,E_WARNING => 'E_WARNING'
				,E_PARSE => 'E_PARSE'
				,E_NOTICE => 'E_NOTICE'
				,E_CORE_ERROR => 'E_CORE_ERROR'
				,E_CORE_WARNING => 'E_CORE_WARNING'
				,E_COMPILE_ERROR => 'E_COMPILE_ERROR'
				,E_COMPILE_WARNING => 'E_COMPILE_WARNING'
				,E_USER_ERROR => 'E_USER_ERROR'
				,E_USER_WARNING => 'E_USER_WARNING'
				,E_USER_NOTICE => 'E_USER_NOTICE'
				,E_STRICT => 'E_STRICT'
				,E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR'
			];
			if (defined('E_DEPRECATED')) {
				$result[E_DEPRECATED] = 'E_DEPRECATED';
			}
			if (defined('E_USER_DEPRECATED')) {
				$result[E_USER_DEPRECATED] = 'E_USER_DEPRECATED';
			}
		}
		return $result;
	}

	/**
	 * @used-by isFatal()
	 * @used-by trace()
	 * @return bool
	 */
	private static function xdebug() {static $r; return $r ? $r : $r = extension_loaded('xdebug');}
}