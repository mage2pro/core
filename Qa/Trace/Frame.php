<?php
namespace Df\Qa\Trace;
use ReflectionMethod as RM;
use ReflectionParameter as RP;
final class Frame extends \Df\Core\O {
	/**
	 * 2015-04-03 Путь к файлу отсутствует при вызовах типа @see call_user_func()
	 * 2023-01-28
	 * 1) The 'file' key can be absent in a stack frame, e.g.:
	 *	{
	 *		"function": "loadClass",
	 *		"class": "Composer\\Autoload\\ClassLoader",
	 *		"type": "->",
	 *		"args": ["Df\\Framework\\Plugin\\App\\Router\\ActionList\\Interceptor"]
	 *	},
	 *	{
	 *		"function": "spl_autoload_call",
	 *		"args": ["Df\\Framework\\Plugin\\App\\Router\\ActionList\\Interceptor"]
	 *	},
	 * 2) @see \Df\Qa\Trace::__construct()
	 * 3) «Argument 1 passed to df_path_rel() must be of the type string, null given,
	 * called in vendor/mage2pro/core/Qa/Trace/Formatter.php on line 37»: https://github.com/mage2pro/core/issues/187
	 * @see df_bt_entry_file()
	 * @see \Df\Sentry\Trace::info()
	 * @used-by self::isPHTML()
	 * @used-by self::url()
	 * @used-by df_sentry()
	 * @used-by \Df\Qa\Trace\Formatter::p()
	 */
	function file():string {return dfc($this, function() {return !($r = (string)$this['file'])? $r : df_path_rel($r);});}

	/**
	 * 2023-07-30
	 * @see df_bt_entry_is_phtml()
	 * @used-by df_sentry()
	 */
	function isPHTML():bool {return dfc($this, function() {return df_is_phtml($this->file());});}

	/**
	 * 2015-04-03 Строка отсутствует при вызовах типа @see call_user_func()
	 * @see df_bt_entry_line()
	 * @used-by df_sentry()
	 * @used-by self::url()
	 * @used-by \Df\Qa\Trace\Formatter::p()
	 */
	function line():int {return (int)$this['line'];}

	/**
	 * 2015-04-03 Для простых функций (не методов) вернёт название функции.
	 * @used-by df_sentry()
	 * @used-by self::methodParameter()
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @used-by \Df\Qa\Method::raiseErrorResult()
	 * @used-by \Df\Qa\Method::raiseErrorVariable()
	 * @used-by \Df\Qa\Trace\Formatter::p()
	 */
	function method():string {return df_cc_method($this->class_(), $this->function_());}

	/**
	 * 2020-02-20
	 * $f could be `include`, `include_once`, `require`, `require_once`:
	 * https://php.net/manual/function.include.php
	 * https://php.net/manual/function.include-once.php
	 * https://php.net/manual/function.require.php
	 * https://php.net/manual/function.require-once.php
	 * https://php.net/manual/function.debug-backtrace.php#111255
	 * They are not functions and will lead to a @see \ReflectionException:
	 * «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
	 * https://php.net/manual/reflectionfunction.construct.php
	 * https://php.net/manual/class.reflectionexception.php
	 * @used-by self::methodParameter()
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @return RM|null
	 */
	function methodR() {return dfc($this, function() {return
		($c = $this->class_()) && ($f = $this->function_()) && !$this->isClosure()
			? df_try(function() use($c, $f) {return new RM($c, $f);}, null)
			: null
	;});}

	/**
	 * $ordering is zero-based
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 */
	function methodParameter(int $ordering):RP {return dfc($this, function(int $ordering) {/** @var RP $r */
		df_assert($m = $this->methodR()); /** @var RM|null $m */
		if ($ordering >= count($m->getParameters())) { # Параметр должен существовать
			df_error(
				"Программист ошибочно пытается получить значение параметра с индексом {$ordering}"
				." метода «{$this->method()}», хотя этот метод принимает всего %d параметров."
				,count($m->getParameters())
			);
		}
		df_assert_lt(count($m->getParameters()), $ordering);
		df_assert(($r = dfa($m->getParameters(), $ordering)) instanceof RP);
		return $r;
	}, [$ordering]);}

	/**
	 * 2023-07-27 "Add GitHub links to backtrace frames": https://github.com/mage2pro/core/issues/285
	 * @used-by \Df\Qa\Trace\Formatter::p()
	 */
	function url():string {return dfc($this, function():string {
		$r = ''; /** @var string $r */
		$pa = df_explode_path($this->file()); /** @var string[] $pa */
		if ('vendor' === array_shift($pa)) {
			$vendor = array_shift($pa); /** @var string $vendor */
			$m = array_shift($pa); /** @var string $m */
			$url = function(string $rep, string $v, string $prefix = '') use($pa):string {return
				df_cc_path("https://github.com/$rep/tree/$v", $prefix, $pa)
				. (!($l = $this->line()) ? '' : "#L$l")
			;};
			# 2023-07-27
			# 1Add GitHub links to backtrace frames related to the `mage2pro/core` repository":
			# https://github.com/mage2pro/core/issues/287
			if ('mage2pro' === $vendor && 'core' === $m) {
				$r = $url('mage2pro/core', df_core_version());
			}
			# 2023-07-27
			# 1) "Add GitHub links to backtrace frames related to the `magento/magento2` repository"
			# https://github.com/mage2pro/core/issues/286
			# 2) @TODO "Add GitHub links to backtrace frames related to the `mage2pro/core` repository":
			# https://github.com/mage2pro/core/issues/287
			elseif ('magento' === $vendor) {
				/** @var string $prefix */
				if ('framework' === $m) {
					$prefix = 'lib/internal/Magento/Framework';
				}
				else {
					$ma = explode('-', $m); /** @var string[] $ma */
					df_assert(df_starts_with($m, 'module-'));
					df_assert_eq('module', array_shift($ma));
					$m = implode(df_ucfirst($ma));
					$prefix = "app/code/Magento/$m";
				}
				$r = $url('magento/magento2', df_magento_version(), $prefix);
			}
		}
		return $r;
	});}

	/**
	 * @see df_bt_entry_class()
	 * @used-by self::methodR()
	 * @used-by self::method()
	 */
	private function class_():string {return df_nts($this['class']);}
	
	/**
	 * @see df_bt_entry_func()
	 * @used-by self::methodR()
	 * @used-by self::method()
	 */
	private function function_():string {return df_nts($this['function']);}

	/**
	 * 2016-07-31
	 * @used-by self::methodR()
	 */
	private function isClosure():bool {return df_ends_with($this->function_(), '{closure}');}

	/**
	 * @used-by df_sentry()
	 * @used-by \Df\Qa\Method::caller()
	 * @used-by \Df\Qa\Trace::__construct()
	 * @param array(string => string|int) $a
	 */
	static function i(array $a):self {return new self($a);}
}