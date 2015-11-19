<?php
namespace Df\Qa\Message;
class Notification extends \Df\Qa\Message {
	/**
	 * @override
	 * @see \Df\Qa\Message::main()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function main() {return $this[self::P__NOTIFICATION];}

	const P__NOTIFICATION = 'notification';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return \Df\Qa\Message\Notification
	 */
	public static function i(array $parameters = []) {return new self($parameters);}
}