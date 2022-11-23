<?php
namespace Df\Core\Text;
final class Regex extends \Df\Core\O {
	/**
	 * @used-by df_preg_match()
	 * @used-by df_preg_int()
	 * Возвращает:
	 * 1) string, если текст соответствует регулярному выражению
	 * 2) string[], если текст соответствует регулярному выражению,
	 * и регулярное выражение содержит несколько пар круглых скобок.
	 * 3) null, если текст не соответствует регулярному выражению
	 * 4) false, если при соответствии произошёл внутренний сбой функции @see preg_match()
	 * @throws \Exception
	 * @return string|string[]|null|bool
	 */
	function match() {return dfc($this, function() {/** @var string|null|bool $r */
		/** @var int|bool $matchResult */ /** @var string[] $matches */
		# Собачка нужна, чтобы подавить warning.
		$matchResult = @preg_match($this->getPattern(), $this->getSubject(), $matches);
		if (false !== $matchResult) {
			if (1 === $matchResult) {
				# Раньше тут стояло:
				# 	$r = dfa($matchResult, 1);
				# что не совсем правильно, потому что если регулярное выражение не содержит круглые скобки,
				# то результирующий массив будет содержать всего один элемент.
				# ПРИМЕР
				# 	регулярное выражение: #[А-Яа-яЁё]#mu
				# 	исходный текст: Категория Яндекс.Маркета
				# 	результат: Array([0] => К)
				# 2015-03-23 Добавил поддержку нескольких пар круглых скобок.
				$r = count($matches) < 3 ? df_last($matches) : df_tail($matches);
			}
			else {
				if ($this->needThrowOnNotMatch()) {
					$this->throwNotMatch();
				}
				$r = null;
			}
		}
		else {
			if ($this[self::$P__THROW_ON_ERROR]) {
				$this->throwInternalError();
			}
			$r = false;
		}
		return $r;
	});}

	/** @return int|null|bool */
	function matchInt() {/** @var string|int|null|bool $r */
		if ($this->test() && df_is_int($r = $this->match())) {
			$r = (int)$r;
		}
		elseif ($this->needThrowOnNotMatch()) {
			$this->throwNotMatch();
		}
		else {
			$r = null;
		}
		return $r;
	}

	/**
	 * @used-by df_preg_test()
	 * @used-by self::matchInt()
	 */
	function test():bool {return dfc($this, function() {return !is_null($this->match()) && (false !== $this->match());});}

	/**
	 * @used-by self::match()
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function getPattern():string {return $this[self::$P__PATTERN];}

	/**
	 * @used-by self::getReportFilePath()
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function getReportFileName():bool {return 'regular-expression-subject.txt';}

	/**
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function getReportFilePath():bool {return df_cc_path(BP, 'var', 'log', $this->getReportFileName());}

	/**
	 * @used-by self::getSubjectReportPart()
	 * @used-by self::getSubjectSplitted()
	 * @used-by self::isSubjectMultiline()
	 * @used-by self::match()
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function getSubject():string {return $this[self::$P__SUBJECT];}

	/**
	 * 2022-10-31
	 * Private constants require PHP ≥ 7.1: https://stackoverflow.com/a/40933237
	 * We need to support PHP 7.0.
	 * @used-by self::getSubjectReportPart()
	 * @used-by self::isSubjectTooLongToReport()
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function getSubjectMaxLinesToReport():int {return 5;}

	/**
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function getSubjectReportPart():string {return dfc($this, function() {return
		!$this->isSubjectTooLongToReport()
		? $this->getSubject()
		: df_cc_n(array_slice($this->getSubjectSplitted(), 0, $this->getSubjectMaxLinesToReport()))
	;});}

	/**
	 * @used-by self::getSubjectReportPart()
	 * @used-by self::isSubjectTooLongToReport()
	 * @return string[]
	 */
	private function getSubjectSplitted():array {return dfc($this, function() {return df_explode_n($this->getSubject());});}

	/**
	 * @used-by self::isSubjectTooLongToReport()
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function isSubjectMultiline():bool {return dfc($this, function() {return df_t()->isMultiline(
		$this->getSubject()
	);});}

	/**
	 * @used-by self::getSubjectReportPart()
	 * @used-by self::getSubjectSplitted()
	 * @used-by self::throwInternalError()
	 * @used-by self::throwNotMatch()
	 */
	private function isSubjectTooLongToReport():bool {return dfc($this, function() {return
		$this->isSubjectMultiline() && $this->getSubjectMaxLinesToReport() < count($this->getSubjectSplitted())
	;});}

	/**
	 * @used-by self::match()
	 * @used-by self::matchInt()
	 */
	private function needThrowOnNotMatch():bool {return $this[self::$P__THROW_ON_NOT_MATCH];}

	/**
	 * @used-by self::match()
	 * @throws \Exception
	 */
	private function throwInternalError():void {
		$numericCode = preg_last_error(); /** @var int $numericCode */
		/** @var string $errorCodeForUser */
		if (!$numericCode) {
			/**
			 * При простых сбоях @see preg_last_error() возвращает 0 (PREG_NO_ERROR).
			 * Например, при таком: df_preg_test('#(#', 'тест');
			 */
			$errorCodeForUser = '';
		}
		else {
			/**
			 * А вот при сложных сбоях @see preg_last_error() возвращает уже какой-то полезный для диагностики код.
			 * Пример из документации:
			 * 		df_preg_test('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar');
			 * https://php.net/manual/function.preg-last-error.php
			 */
			$textCode = dfa(self::getErrorCodeMap(), $numericCode); /** @var string|null $textCode */
			$errorCodeForUser = ' ' . ($textCode ? $textCode : 'с кодом ' . $numericCode);
		}
		/** @var string $m */
		if (!$this->isSubjectMultiline()) {
			$m =
				"При применении регулярного выражения «{$this->getPattern()}»"
				. " к строке «{$this->getSubject()}» произошёл сбой{$errorCodeForUser}."
			;
		}
		elseif (!$this->isSubjectTooLongToReport()) {
			$m =
				"При применении регулярного выражения «{$this->getPattern()}»"
				." произошёл сбой{$errorCodeForUser}."
				."\nТекст, к которому применялось регулярное выражение:"
				."\nНАЧАЛО ТЕКСТА:\n{$this->getSubject()}\nКОНЕЦ ТЕКСТА"
			;
		}
		else {
			df_report($this->getReportFileName(), $this->getSubject());
			$m =
				"При применении регулярного выражения «{$this->getPattern()}»"
				." произошёл сбой{$errorCodeForUser}."
				."\nТекст, к которому применялось регулярное выражение,"
				." смотрите в файле {$this->getReportFilePath()}."
				."\nПервые {$this->getSubjectMaxLinesToReport()} строк текста:"
				."\nНАЧАЛО:\n{$this->getSubjectReportPart()}\nКОНЕЦ"
			;
		}
		df_error($m);
	}

	/**
	 * @used-by self::match()
	 * @used-by self::matchInt()
	 * @throws \Exception
	 */
	private function throwNotMatch():void {/** @var string $m */
		if (!$this->isSubjectMultiline()) {
			$m = "Строка «{$this->getSubject()}» не отвечает регулярному выражению «{$this->getPattern()}».";
		}
		elseif (!$this->isSubjectTooLongToReport()) {
			$m =
				"Указанный ниже текст не отвечает регулярному выражению «{$this->getPattern()}»:"
				."\nНАЧАЛО ТЕКСТА:\n{$this->getSubject()}\nКОНЕЦ ТЕКСТА"
			;
		}
		else {
			df_report($this->getReportFileName(), $this->getSubject());
			$m =
				"Текст не отвечает регулярному выражению «{$this->getPattern()}»."
				."\nТекст смотрите в файле {$this->getReportFilePath()}."
				."\nПервые {$this->getSubjectMaxLinesToReport()} строк текста:"
				."\nНАЧАЛО:\n{$this->getSubjectReportPart()}\nКОНЕЦ"
			;
		}
		df_error($m);
	}

	/** @var string */
	private static $P__PATTERN = 'pattern';
	/** @var string */
	private static $P__SUBJECT = 'subject';

	/**
	 * @used-by self::i()
	 * @used-by self::match()
	 * @var string
	 */
	private static $P__THROW_ON_ERROR = 'throw_on_error';

	/**
	 * @used-by self::i()
	 * @used-by self::needThrowOnNotMatch()
	 * @var string
	 */
	private static $P__THROW_ON_NOT_MATCH = 'throw_on_not_match';

	/**
	 * @used-by df_preg_int()
	 * @used-by df_preg_match()
	 * @used-by df_preg_test()
	 */
	static function i(string $pattern, string $subject, bool $throwOnError = true, bool $throwOnNotMatch = false):self {return
		new self([
			self::$P__PATTERN => $pattern
			, self::$P__SUBJECT => $subject
			, self::$P__THROW_ON_ERROR => $throwOnError
			, self::$P__THROW_ON_NOT_MATCH => $throwOnNotMatch
		])
	;}

	/**
	 * Возвращает соответствие между числовыми кодами, возвращаемыми функцией @see preg_last_error(),
	 * и их символьными соответствиями:
	 *	PREG_NO_ERROR
	 *	PREG_INTERNAL_ERROR
	 *	PREG_BACKTRACK_LIMIT_ERROR
	 *	PREG_RECURSION_LIMIT_ERROR
	 *	PREG_BAD_UTF8_ERROR
	 *	PREG_BAD_UTF8_OFFSET_ERROR
	 * @used-by self::throwInternalError()
	 * @return array(int => string)
	 */
	private static function getErrorCodeMap():array {return dfcf(function() {return array_filter(
		df_map_kr(function($s, $n) {return
			[$n, !df_ends_with($s, '_ERROR') ? null : $s]
		;}, get_defined_constants(true)['pcre'])
	);});}
}