<?php
namespace Df\Qa\Message;
use Df\Qa\State;
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
		/** @var int $count */
		$count = count($this->states());
		return implode(df_map_k($this->states(), function($index, State $state) use($count) {
			$index++;
			/** @var string $result */
			$result = (string)$state;
			if ($index !== $count) {
				/** @var string $indexS */
				$indexS = (string)$index;
				/** @var int $indexLength */
				$indexLength = strlen($indexS);
				/** @var int $delimiterLength */
				$delimiterLength = 36;
				/** @var int $fillerLength */
				$fillerLength = $delimiterLength - $indexLength;
				/** @var int $fillerLengthL */
				$fillerLengthL = floor($fillerLength / 2);
				/** @var int $fillerLengthR */
				$fillerLengthR = $fillerLength - $fillerLengthL;
				/** @var string $delimiter */
				$delimiter = str_repeat('*', $fillerLengthL) . $indexS . str_repeat('*', $fillerLengthR);
				$result .= "\n" . $delimiter . "\n";
			}
			return $result;
		}));
	}

	/**
	 * @override
	 * @see \Df\Qa\Message::postface()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function postface() {return $this->traceS();}

	/**
	 * @override
	 * @see \Df\Qa\Message::preface()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function preface() {return $this[self::P__ADDITIONAL_MESSAGE];}

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
				/** @var array(string => string|int) $stateA */
				$state = State::i($stateA, $state, $this->cfg(self::P__SHOW_CODE_CONTEXT, true));
				$result[]= $state;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const P__ADDITIONAL_MESSAGE = 'additional_message';
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}