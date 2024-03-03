<?php
namespace Df\Qa;
use Df\Qa\Trace\Frame as F;
use ArrayIterator as AI;
# 2020-02-27
final class Trace implements \IteratorAggregate, \Countable {
	/**
	 * 2020-02-27
	 * @used-by df_bt_s()
	 * @used-by \Df\Qa\Failure::postface()
	 * @param array(string => int|string) $ff
	 */
	function __construct(array $ff) {
		$ff = df_bt_filter_head($ff, [
			# 2024-03-04
			# "Skip the leading `df_error()` from the logged backtraces": https://github.com/mage2pro/core/issues/355
			'df_error'
			# 2023-07-26
			# "Skip the leading `df_error_create()` from the logged backtraces": https://github.com/mage2pro/core/issues/262
			,'df_error_create'
			# 2023-07-27 "Skip the leading `df_log()` from the logged backtraces": https://github.com/mage2pro/core/issues/284
			,'df_log'
		]);
		/**
		 * 2017-07-01
		 * Сегодня при обработке исключительной ситуации при запуске теста из PHPUnit
		 * столкнулся с проблемой, что стек вызовов внутри файла PHPUnit в формате Phar
		 * в моём случае содержал какие-то бинарные символы, из-за которых падала моя функция @see df_trim()
		 * @see \Df\Zf\Filter\StringTrim::_splitUtf8()
		 * Я эту проблему решил тем, что теперь df_trim() по-умолчанию
		 * в случае исключительной ситуации просто возвращет исходную строку,
		 * а не возбуждает исключительную ситуацию.
		 * Однако мне в стеке вызовов в любом случае не нужна бинарная каша,
		 * поэтому я отсекаю ту часть стека, которая находится внутри Phar.
		 * 2023-07-26 "Implement `df_filter_tail()`": https://github.com/mage2pro/core/issues/263
		 */
		$ff = df_filter_tail($ff, function(array $f):bool {return df_starts_with(df_bt_entry_file($f), 'phar://');});
		$this->_frames = df_map($ff, function(array $f):F {return F::i($f);});
	}

	/**
	 * 2020-02-27
	 * @override
	 * @see \Countable::count() https://php.net/manual/countable.count.php
	 * @used-by \Df\Qa\Trace\Formatter::p()
	 */
	function count():int {return count($this->_frames);}

	/**
	 * 2020-02-27
	 * @override
	 * @see \IteratorAggregate::getIterator() https://php.net/manual/iteratoraggregate.getiterator.php
	 * @used-by \Df\Qa\Trace\Formatter::p()
	 */
	function getIterator():AI {return new AI($this->_frames);}

	/**
	 * 2020-02-27
	 * @used-by self::__construct()
	 * @used-by self::count()
	 * @used-by self::getIterator()
	 * @var F[]
	 */
	private $_frames;
}