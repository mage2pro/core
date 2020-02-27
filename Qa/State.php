<?php
namespace Df\Qa;
use ReflectionFunction as RF;
use ReflectionFunctionAbstract as RFA;
use ReflectionMethod as RM;
use ReflectionParameter as RP;
final class State extends \Df\API\Document {
	/**
	 * @used-by \Df\Qa\Message\Failure::traceS()
	 * @override
	 * @return string
	 */
	function __toString() {return dfc($this, function() {/** @var string $r */
		/**
		 * Метод @see __toString() не имеет права возбуждать исключительных ситуаций.
		 * "Fatal error: Method __toString() must not throw an exception": http://stackoverflow.com/questions/2429642
		 */
		try {
			$resultA = array_filter(array_map([__CLASS__, 'param'], [
				['Location', df_cc(':', df_path_relative($this->filePath()), $this->line())]
				,['Caller', !$this->_next ? '' : $this->_next->methodName()]
				,['Callee', $this->methodName()]
			])); /** @var string[] $resultA */ /** @uses param() */
			if ($this[self::$P__SHOW_CONTEXT] && $this->context()) {
				$resultA[]= self::param(['Context', "\n{$this->context()}"]);
			}
			$r = df_cc_n($resultA);
		}
		catch (\Exception $e) {
			$r = df_ets($e);
			/**
			 * 2020-02-20
			 * 1) «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
			 * 2) It is be dangerous to call @see df_log_e() here, because it will inderectly return us here,
			 * and it could be an infinite loop.
			 */
			static $loop = false;
			if ($loop) {
				df_log_l($this, "$r\n{$e->getTraceAsString()}", df_class_l($this));
			}
			else {
				$loop = true;
				df_log_e($e, $this);
				$loop = false;
			}
		}
		return $r;
	});}

	/**
	 * 2020-02-20
	 * $f could be `include`, `include_once`, `require`, ``require_once``:
	 * https://www.php.net/manual/function.include.php
	 * https://www.php.net/manual/function.include-once.php
	 * https://www.php.net/manual/function.require.php
	 * https://www.php.net/manual/function.require-once.php
	 * https://www.php.net/manual/function.debug-backtrace.php#111255
	 * They are not functions and will lead to a @see \ReflectionException:
	 * «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
	 * https://www.php.net/manual/reflectionfunction.construct.php
	 * https://www.php.net/manual/class.reflectionexception.php
	 * @see functionA()
	 * @used-by functionA()
	 * @used-by methodParameter()
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @return RM|null
	 */
	function method() {return dfc($this, function() {return
		($c = $this->className()) && ($f = $this->functionName()) && !$this->isClosure()
			? df_try(function() use($c, $f) {return new RM($c, $f);}, null)
			: null
	;});}

	/**
	 * 2015-04-03 Для простых функций (не методов) вернёт название функции.
	 * @used-by __toString()
	 * @used-by methodParameter()
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @used-by \Df\Qa\Method::raiseErrorResult()
	 * @used-by \Df\Qa\Method::raiseErrorVariable()
	 * @return string
	 */
	function methodName() {return df_cc_method($this->className(), $this->functionName());}

	/**
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @param int $ordering  		zero-based
	 * @return RP
	 */
	function methodParameter($ordering) {return dfc($this, function($ordering) {/** @var RP $r */
		df_param_integer($ordering, 0);
		df_assert($m = $this->method()); /** @var RM|null $m */
		if ($ordering >= count($m->getParameters())) { // Параметр должен существовать
			df_error(
				"Программист ошибочно пытается получить значение параметра с индексом {$ordering}"
				. " метода «{$this->methodName()}», хотя этот метод принимает всего %d параметров."
				, count($m->getParameters())
			);
		}
		df_assert_lt(count($m->getParameters()), $ordering);
		df_assert(($r = dfa($m->getParameters(), $ordering)) instanceof RP);
		return $r;
	}, [$ordering]);}

	/**
	 * @used-by method()
	 * @used-by methodName()
	 * @return string
	 */
	private function className() {return df_nts($this['class']);}

	/** @return string */
	private function context() {return dfc($this, function() {
		$r = ''; /** @var string $r */
		if (is_file($this->filePath()) && $this->line()) {
			$fileContents = file($this->filePath());/** @var string[] $fileContents */
			if (is_array($fileContents)) {
				// Перенос строки здесь не нужен, потому что строки с кодом уже содержат переносы на следующую стоку:
				// http://php.net/manual/function.file.php
				$fileLength = count($fileContents); /** @var int $fileLength */
				$radius = 8; /** @var int $radius */
				$start = max(0, $this->line() - $radius); /** @var int $start */
				$end = min($fileLength, $start + 2 * $radius); /** @var int $end */
				if ($this->_next) { // 2016-07-31 Нам нужна информация именно функции next (caller).
					$func = $this->_next->functionA(); /** @var RFA|null $func */
					/**
					 * 2016-07-31
					 * Если @uses \ReflectionFunctionAbstract::isInternal() вернёт true,
					 * то @uses \ReflectionFunctionAbstract::getStartLine() и
					 * @uses \ReflectionFunctionAbstract::getEndLine() вернут false.
					 * http://stackoverflow.com/questions/2222142#comment25428181_2222404
					 * isInternal() === TRUE means ->getFileName() and ->getStartLine() will return FALSE
					 */
					if ($func && !$func->isInternal()) {
						$fStart = df_assert_nef($func->getStartLine()); /** @var int $fStart */
						$fEnd = df_assert_nef($func->getEndLine()); /** @var int $fEnd */
						// 2016-07-31
						// http://stackoverflow.com/a/7027198
						// It's actually - 1, otherwise you wont get the function() block.
						$start = max($start, $fStart - 1);
						$end = min($end, $fEnd);
					}
				}
				$r = df_trim(implode(array_slice($fileContents, $start, $end - $start)));
			}
		}
		return $r;
	});}

	/**
	 * 2015-04-03 Путь к файлу отсутствует при вызовах типа @see call_user_func()
	 * @used-by __toString()
	 * @used-by context()
	 * @return string|null
	 */
	private function filePath() {return $this['file'];}

	/**
	 * 2016-07-31 Без проверки на closure будет сбой: «Function Df\Config\{closure}() does not exist».
	 * 2020-02-20
	 * $f could be `include`, `include_once`, `require`, ``require_once``:
	 * https://www.php.net/manual/function.include.php
	 * https://www.php.net/manual/function.include-once.php
	 * https://www.php.net/manual/function.require.php
	 * https://www.php.net/manual/function.require-once.php
	 * https://www.php.net/manual/function.debug-backtrace.php#111255
	 * They are not functions and will lead to a @see \ReflectionException:
	 * «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
	 * https://www.php.net/manual/reflectionfunction.construct.php
	 * https://www.php.net/manual/class.reflectionexception.php
	 * @see method()
	 * @used-by context()
	 * @return RFA|RF|RM|null
	 */
	private function functionA() {return dfc($this, function() {return $this->method() ?: (
		(!($f = $this->functionName())) || $this->isClosure() ? null : df_try(function() use($f) {return new RF($f);}, null)
	);});}
	
	/**
	 * @used-by method()
	 * @used-by methodName()
	 * @return string
	 */
	private function functionName() {return df_nts($this['function']);}

	/**
	 * 2016-07-31
	 * @used-by functionA()
	 * @used-by method()
	 * @return bool
	 */
	private function isClosure() {return df_ends_with($this->functionName(), '{closure}');}

	/**
	 * 2015-04-03 Строка отсутствует при вызовах типа @see call_user_func()
	 * @used-by __toString()
	 * @used-by context()
	 * @return int|null
	 */
	private function line() {return $this['line'];}

	/**
	 * @used-by __toString()
	 * @used-by i()
	 * @var State|null
	 */
	private $_next;

	/** @var string */
	private static $P__SHOW_CONTEXT = 'show_context';

	/**
	 * @used-by \Df\Qa\Method::caller()
	 * @used-by \Df\Qa\Message\Failure::states()
	 * @param array(string => string|int) $stateA
	 * @param State|null $previous [optional]
	 * @param bool $showContext [optional]
	 * @return self
	 */
	static function i(array $stateA, State $previous = null, $showContext = false) { /** @var self $r */
		$r = new self($stateA + [self::$P__SHOW_CONTEXT => $showContext]);
		if ($previous) {
			$previous->_next = $r;
		}
		return $r;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by __toString()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param array $param
	 * @return string|null
	 */
	private static function param(array $param) {/** @var string|null $r */ /** @var string|null $v */
		if (!($v = $param[1])) {
			$r = null;
		}
		else {
			$label = $param[0]; /** @var string $label */
			$pad = df_pad(' ', 12 - mb_strlen($label)); /** @var string $pad */
			$r = "{$label}:{$pad}{$v}";
		}
		return $r;
	}
}