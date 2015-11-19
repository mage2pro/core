<?php
namespace Df\Qa\Message;
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
	public final function traceS() {return $this->sections($this->states());}

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
	 * @see \Df\Qa\Message_Failure_Exception::stackLevel()
	 * @see \Df\Qa\Message_Failure_Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}

	/** @return \Df\Qa\State[] */
	private function states() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\Qa\State[] $result */
			$result = [];
			/** @var array(array(string => string|int)) $trace */
			$trace = array_slice($this->trace(), $this->stackLevel());
			/** @var \Df\Qa\State|null $state */
			$state = null;
			foreach ($trace as $stateA) {
				/** @var array(string => string|int) $stateA */
				$state = \Df\Qa\State::i($stateA, $state, $this->cfg(self::P__SHOW_CODE_CONTEXT, true));
				$result[]= $state;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const P__ADDITIONAL_MESSAGE = 'additional_message';
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}