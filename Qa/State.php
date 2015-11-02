<?php
namespace Df\Qa;
class State extends \Df\Core\O {
	/**
	 * @used-by \Df\Qa\Message_Failure::traceS()
	 * @override
	 * @return string
	 */
	public function __toString() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Метод @see __toString() не имеет права возбуждать исключительных ситуаций.
			 * Fatal error: Method __toString() must not throw an exception
			 * http://stackoverflow.com/questions/2429642/why-its-impossible-to-throw-exception-from-tostring
			 */
			try {
				/** @var string[] $resultA */
				/** @uses param() */
				$resultA = array_filter(array_map(array(__CLASS__, 'param'), array(
					array('Файл', str_replace(DIRECTORY_SEPARATOR, '/', df_trim_text_left($this->filePath(), BP . DIRECTORY_SEPARATOR)))
					,array('Строка', $this->line())
					,array('Субъект', !$this->_next ? '' : $this->_next->methodName())
					,array('Объект', $this->methodName())
				)));
				if (false && $this[self::$P__SHOW_CONTEXT] && $this->context()) {
					$resultA[]= self::param(array('Контекст', "\n" . $this->context()));
				}
				$this->{__METHOD__} = df_concat_n($resultA);
			}
			catch (\Exception $e) {
				df_log(df_ets($e));
				$this->{__METHOD__} = df_ets($e);
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @param int $paramOrdering  		порядковый номер параметра метода
	 * @return \ReflectionParameter
	 */
	public function methodParameter($paramOrdering) {
		df_param_integer($paramOrdering, 0);
		if (!isset($this->{__METHOD__}[$paramOrdering])) {
			// Метод должен существовать
			df_assert($this->method());
			// Параметр должен существовать
			if ($paramOrdering >= count($this->method()->getParameters())) {
				df_error(
					"Программист ошибочно пытается получить значение параметра с индексом {$paramOrdering}"
					. " метода «{$this->methodName()}», хотя этот метод принимает всего %d параметров."
					, count($this->method()->getParameters())
				);
			}
			df_assert_lt(count($this->method()->getParameters()), $paramOrdering);
			/** @var \ReflectionParameter $result */
			$result = df_a($this->method()->getParameters(), $paramOrdering);
			df_assert($result instanceof \ReflectionParameter);
			$this->{__METHOD__}[$paramOrdering] = $result;
		}
		return $this->{__METHOD__}[$paramOrdering];
	}

	/**
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @return \ReflectionMethod|null
	 */
	public function method() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				($this->className() && $this->functionName())
				? new \ReflectionMethod($this->className(), $this->functionName())
				: null
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * @used-by \Df\Qa\Method::raiseErrorParam()
	 * @used-by \Df\Qa\Method::raiseErrorResult()
	 * @used-by \Df\Qa\Method::raiseErrorVariable()
	 * @return string
	 */
	public function methodName() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2015-04-03
			 * Надо использовать именно @uses df_concat_clean(),
			 * потому что для простых функций (не методов)
			 * @uses className() вернёт пустое значение.
			 */
			$this->{__METHOD__} = df_concat_clean('::', $this->className(), $this->functionName());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function className() {return $this->cfg('class', '');}

	/** @return string */
	private function context() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			if (is_file($this->filePath()) && $this->line()) {
				/** @var string[] $fileContents */
				$fileContents = file($this->filePath());
				if (is_array($fileContents)) {
					/**
					 * Перенос строки здесь не нужен,
					 * потому что строки с кодом
					 * уже содержат переносы на следующую стоку
					 * http://php.net/manual/function.file.php
					 */
					/** @var int $radius */
					$radius = 8;
					$result = df_trim(
						implode(array_slice($fileContents, max(0, $this->line() - $radius), 2 * $radius))
						,$charlist = "\r\n"
					);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-04-03
	 * Путь к файлу отсутствует при вызовах типа @see call_user_func()
	 * @used-by __toString()
	 * @used-by context()
	 * @return string|null
	 */
	private function filePath() {return $this->cfg('file');}

	/**
	 * @used-by method()
	 * @used-by methodName()
	 * @return string
	 */
	private function functionName() {return $this->cfg('function', '');}

	/**
	 * 2015-04-03
	 * Строка отсутствует при вызовах типа @see call_user_func()
	 * @used-by __toString()
	 * @used-by context()
	 * @return int|null
	 */
	private function line() {return $this->cfg('line');}

	/**
	 * @used-by __toString()
	 * @used-by i()
	 * @var \Df\Qa\State|null
	 */
	private $_next;
	/** @var string */
	private static $P__SHOW_CONTEXT = 'show_context';

	/**
	 * @used-by \Df\Qa\Method::caller()
	 * @param array(string => string|int) $stateA
	 * @param \Df\Qa\State|null $previous [optional]
	 * @param bool $showContext [optional]
	 * @return \Df\Qa\State
	 */
	public static function i(array $stateA, \Df\Qa\State $previous = null, $showContext = false) {
		$result = new self($stateA + array(self::$P__SHOW_CONTEXT => $showContext));
		if ($previous) {
			$previous->_next = $result;
		}
		return $result;
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
	private static function param(array $param) {
		/** @var string|null $result */
		/** @var string|null $value */
		$value = $param[1];
		if (!$value) {
			$result = null;
		}
		else {
			/** @var string $label */
			$label = $param[0];
			/** @var string $pad */
			$pad = df_pad(' ', 12 - mb_strlen($label));
			$result = "{$label}:{$pad}{$value}";
		}
		return $result;
	}
}