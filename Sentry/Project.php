<?php
// 2017-01-27
namespace Df\Sentry;
final class Project {
	/**
	 * 2017-01-27
	 * https://docs.sentry.io/quickstart/#configure-the-dsn
	 * @param int $id
	 * @param string $keyPublic
	 * @param string $keySecret
	 */
	private function __construct($id, $keyPublic, $keySecret) {
		$this->_id = $id;
		$this->_keyPublic = $keyPublic;
		$this->_keySecret = $keySecret;
	}

	/**
	 * 2017-01-27
	 * @used-by __construct()
	 * @var int
	 */
	private $_id;

	/**
	 * 2017-01-27
	 * @used-by __construct()
	 * @var string
	 */
	private $_keyPublic;

	/**
	 * 2017-01-27
	 * @used-by __construct()
	 * @var string
	 */
	private $_keySecret;

	/**
	 * 2017-01-27
	 * @param int $id
	 * @param string $keyPublic
	 * @param string $keySecret
	 * @return self
	 */
	public static function s($id, $keyPublic, $keySecret) {return
		dfcf(function($id, $keyPublic, $keySecret) {return
			new self($id, $keyPublic, $keySecret)
		;}, func_get_args())
	;}
}