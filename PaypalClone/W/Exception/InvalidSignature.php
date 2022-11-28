<?php
namespace Df\PaypalClone\W\Exception;
use Df\PaypalClone\W\Event as Ev;
/**
 * 2017-10-03
 * @used-by \Df\PaypalClone\W\Event::validate()
 * @used-by \Dfe\YandexKassa\Result::attributes()
 */
final class InvalidSignature extends \Df\Payment\W\Exception\Critical {
	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Payment\W\Exception::__construct()
	 * @used-by \Df\Payment\W\F::c()
	 */
	function __construct(Ev $ev, string $expected, string $provided) {
		$this->_expected = $expected;
		$this->_provided = $provided;
		parent::__construct($ev->m(), $ev);
	}

	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by \Df\Payment\W\Handler::handle()
	 */
	function message():string {return $this->_message(df_my());}

	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Core\Exception::messageD()
	 * @used-by df_xtsd()
	 * @used-by \Df\Core\Exception::messageL()
	 * @used-by \Df\Core\Exception::messageSentry()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @used-by \Df\Qa\Failure\Exception::main()
	 * @used-by \Df\Sentry\Client::captureException()
	 */
	function messageD():string {return $this->_message(true);}

	/**
	 * 2017-10-03
	 * @override
	 * @see \Df\Core\Exception::sentryType()
	 * @used-by \Df\Sentry\Client::captureException()
	 */
	function sentryType():string {return 'Invalid signature';}

	/**
	 * 2017-10-03
	 * @used-by self::message()
	 * @used-by self::messageD()
	 */
	private function _message(bool $full):string {return 'Invalid signature.' . (!$full ? '' :
		"\nExpected: «{$this->_expected}».\nProvided: «{$this->_provided}»."
	);}

	/**
	 * 2017-10-03
	 * @used-by self::__construct()
	 * @var string
	 */
	private $_expected;

	/**
	 * 2017-10-03
	 * @used-by self::__construct()
	 * @var string
	 */
	private $_provided;
}