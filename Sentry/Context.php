<?php
namespace Df\Sentry;
class Context {
	/**
	 * 2020-06-27
	 * @used-by \Df\Sentry\Client::__construct()
	 */
	function __construct()
	{
		$this->clear();
	}

	/**
	 * Clean up existing context.
	 */
	function clear()
	{
		$this->tags = [];
		$this->extra = [];
		$this->user = null;
	}

	/**
	 * 2017-01-10
	 * @used-by clear()
	 * @used-by \Df\Sentry\Client::capture()
	 * @used-by \Df\Sentry\Client::extra_context()
	 * @var array(string => mixed)
	 */
	public $extra;
	/**
	 * 2017-01-10
	 * @used-by clear()
	 * @used-by \Df\Sentry\Client::capture()
	 * @used-by \Df\Sentry\Client::tags()
	 * @var array(string => string)
	 */
	public $tags;
	/**
	 * 2017-01-10
	 * @used-by clear()
	 * @used-by \Df\Sentry\Client::get_user_data()
	 * @used-by \Df\Sentry\Client::user()
	 * @var array(string => mixed)
	 */
	public $user;
}
