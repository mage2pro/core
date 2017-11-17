<?php
namespace Df\PaypalClone\W\Exception;
use Df\PaypalClone\W\Event as Ev;
/**
 * 2017-10-03
 * @used-by \Df\PaypalClone\W\Event::validate()
 * @used-by \Dfe\YandexKassa\Result::__toString()
 */
final class InvalidSignature extends \Df\Payment\W\Exception\Critical {
	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Payment\W\Exception::__construct()
	 * @used-by \Df\Payment\W\F::c()
	 * @param Ev $ev
	 * @param string $expected
	 * @param string $provided
	 */
	function __construct(Ev $ev, $expected, $provided) {
		$this->_expected = $expected;
		$this->_provided = $provided;
		parent::__construct($ev->m(), $ev);
	}

	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @used-by \Dfe\YandexKassa\Result::__toString()
	 * @return string
	 */
	function message() {return $this->_message(df_my());}

	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Core\Exception::messageD()
	 * @used-by \Df\Core\Exception::messageL()
	 * @used-by \Df\Core\Exception::messageSentry()
	 * @return string
	 */
	function messageD() {return $this->_message(true);}

	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Core\Exception::sentryType()
	 * @used-by \Df\Sentry\Client::captureException()
	 * @return string
	 */
	function sentryType() {return 'Invalid signature';}

	/**
	 * 2017-10-03
	 * @used-by message()
	 * @used-by messageD()
	 * @param bool $full
	 * @return string
	 */
	private function _message($full) {return 'Invalid signature.' . (!$full ? null :
		"\nExpected: «{$this->_expected}».\nProvided: «{$this->_provided}»."
	);}

	/**
	 * 2017-10-03
	 * @used-by __construct()
	 * @var string
	 */
	private $_expected;

	/**
	 * 2017-10-03
	 * @used-by __construct()
	 * @var string
	 */
	private $_provided;
}