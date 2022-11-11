<?php
namespace Df\Sentry;
class Context {
	/**
	 * 2020-06-27
	 * @used-by \Df\Sentry\Client::__construct()
	 */
	function __construct() {$this->extra = []; $this->tags = []; $this->user = null;}

	/**
	 * 2017-01-10
	 * @used-by self::clear()
	 * @used-by \Df\Sentry\Client::capture()
	 * @used-by \Df\Sentry\Client::extra_context()
	 * @var array(string => mixed)
	 */
	public $extra;
	/**
	 * 2017-01-10
	 * @used-by self::clear()
	 * @used-by \Df\Sentry\Client::capture()
	 * @used-by \Df\Sentry\Client::tags()
	 * @var array(string => string)
	 */
	public $tags;
	/**
	 * 2017-01-10
	 * @used-by self::clear()
	 * @used-by \Df\Sentry\Client::get_user_data()
	 * @used-by \Df\Sentry\Client::user()
	 * @var array(string => mixed)
	 */
	public $user;
}
