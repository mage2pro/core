<?php
namespace Df\Qa;
use ReflectionFunction as RF;
use ReflectionFunctionAbstract as RFA;
use ReflectionMethod as RM;
use ReflectionParameter as RP;
class State extends \Df\Core\O {
	/**
	 * @used-by \Df\Qa\Message_Failure::traceS()
	 * @override
	 * @return string
	 */
	function __toString() {return dfc($this, function() {/** @var string $r */
		/**
		 * Метод @see __toString() не имеет права возбуждать исключительных ситуаций.
		 * "Fatal error: Method __toString() must not throw an exception": http://stackoverflow.com/questions/2429642
		 */
		try {
			/** @var string[] $resultA */ /** @uses param() */
			$resultA = array_filter(array_map([__CLASS__, 'param'], [
				['File', str_replace(DIRECTORY_SEPARATOR, '/', df_trim_text_left($this->filePath(), BP . DIRECTORY_SEPARATOR))]
				,['Line', $this->line()]
				,['Caller', !$this->_next ? '' : $this->_next->methodName()]
				,['Callee', $this->methodName()]
			]));
			if ($this[self::$P__SHOW_CONTEXT] && $this->context()) {
				$resultA[]= self::param(['Context', "\n" . $this->context()]);
			}
			$r = df_cc_n($resultA);
		}
		catch (\Exception $e) {df_log($r = df_ets($e));}
		return $r;
	});}

	/**
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @param int $ordering  		zero-based
	 * @return RP
	 */
	function methodParameter($ordering) {return dfc($this, function($ordering) {/** @var RP $r */
		df_param_integer($ordering, 0);
		df_assert($this->method()); // Метод должен существовать
		if ($ordering >= count($this->method()->getParameters())) { // Параметр должен существовать
			df_error(
				"Программист ошибочно пытается получить значение параметра с индексом {$ordering}"
				. " метода «{$this->methodName()}», хотя этот метод принимает всего %d параметров."
				, count($this->method()->getParameters())
			);
		}
		df_assert_lt(count($this->method()->getParameters()), $ordering);
		df_assert(($r = dfa($this->method()->getParameters(), $ordering)) instanceof RP);
		return $r;
	}, [$ordering]);}

	/**
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @return RM|null
	 */
	function method() {return dfc($this, function() {return
		($c = $this->className()) && ($f = $this->functionName()) && !$this->isClosure() ? new RM($c, $f) : null
	;});}

	/**
	 * 2015-04-03
	 * Для простых функций (не методов) вернёт название функции.
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @used-by \Df\Qa\Method::raiseErrorResult()
	 * @used-by \Df\Qa\Method::raiseErrorVariable()
	 * @return string
	 */
	function methodName() {return df_cc_method($this->className(), $this->functionName());}

	/** @return string */
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
	 * 2015-04-03
	 * Путь к файлу отсутствует при вызовах типа @see call_user_func()
	 * @used-by __toString()
	 * @used-by context()
	 * @return string|null
	 */
	private function filePath() {return $this->cfg('file');}

	/**
	 * 2016-07-31
	 * Без проверки на closure будет сбой:
	 * «Function Df\Config\{closure}() does not exist».
	 * @return RFA|RF|RM|null
	 */
	private function functionA() {return dfc($this, function() {return $this->method() ?: (
		($f = $this->functionName()) && !$this->isClosure() ? new RF($f) : null
	);});}
	
	/**
	 * @used-by method()
	 * @used-by methodName()
	 * @return string
	 */
	private function functionName() {return df_nts($this['function']);}

	/**
	 * 2016-07-31
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