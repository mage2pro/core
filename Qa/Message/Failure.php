<?php
namespace Df\Qa\Message;
use Df\Qa\State;
/**
 * @see \Df\Qa\Message\Failure\Error
 * @see \Df\Qa\Message\Failure\Exception
 */
abstract class Failure extends \Df\Qa\Message {
	/**
	 * @abstract
	 * @used-by states()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace();

	/**
	 * @used-by df_exception_get_trace()
	 * @used-by postface()
	 * @return string
	 */
	public final function traceS() {
		$count = count($this->states()); /** @var int $count */
		return implode(df_map_k($this->states(), function($index, State $state) use($count) {
			$index++;
			$r = (string)$state; /** @var string $r */
			if ($index !== $count) {
				$indexS = (string)$index; /** @var string $indexS */
				$indexLength = strlen($indexS); /** @var int $indexLength */
				$delimiterLength = 36; /** @var int $delimiterLength */
				$fillerLength = $delimiterLength - $indexLength; /** @var int $fillerLength */
				$fillerLengthL = floor($fillerLength / 2); /** @var int $fillerLengthL */
				$fillerLengthR = $fillerLength - $fillerLengthL; /** @var int $fillerLengthR */
				$r .= "\n" . str_repeat('*', $fillerLengthL) . $indexS . str_repeat('*', $fillerLengthR) . "\n";
			}
			return $r;
		}));
	}

	/**
	 * @override
	 * @see \Df\Qa\Message::postface()
	 * @used-by \Df\Qa\Message::report()
	 * @used-by \Df\Qa\Message\Failure\Exception::postface()
	 * @see \Df\Qa\Message\Failure\Exception::postface()
	 * @return string
	 */
	protected function postface() {return $this->traceS();}

	/**
	 * @used-by states()
	 * @see \Df\Qa\Message\Failure\Exception::stackLevel()
	 * @see \Df\Qa\Message\Failure\Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}

	/** @return State[] */
	private function states() {
		if (!isset($this->{__METHOD__})) {
			/** @var State[] $result */
			$result = [];
			/** @var array(array(string => string|int)) $trace */
			$trace = array_slice($this->trace(), $this->stackLevel());
			/** @var State|null $state */
			$state = null;
			foreach ($trace as $stateA) {
				/**
				 * 2017-07-01
				 * Сегодня при обработке исключительной ситуации при запуске теста из PHPUnit
				 * столкнулся с проблемой, что стек вызовов внутри файла PHPUnit в формате Phar
				 * в моём случае содержал какие-то бинарные символы,
				 * из-за которых падала моя функция @see df_trim()
				 * @see \Df\Zf\Filter\StringTrim::_splitUtf8()
				 * Я эту проблему решил тем, что теперь df_trim() по-умолчанию
				 * в случае исключительной ситуации просто возвращет исходную строку,
				 * а не возбуждает исключительную ситуацию.
				 * Однако мне в стеке вызовов в любом случае не нужна бинарная каша,
				 * поэтому я отсекаю ту часть стека, которая находится внутри Phar.
				 */
				if (df_starts_with(dfa($stateA, 'file'), 'phar://')) {
					break;
				}
				/** @var array(string => string|int) $stateA */
				$state = State::i($stateA, $state, $this->cfg(self::P__SHOW_CODE_CONTEXT, true));
				$result[]= $state;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by states()
	 * @used-by df_exception_get_trace()
	 * @used-by df_log_e()
	 */
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}