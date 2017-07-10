<?php
namespace Df\Sentry\Breadcrumbs;
class ErrorHandler
{
    private $existingHandler;

    /**
     * @var \Df\Sentry\Client the client object that sends the message to the server
     */
    protected $ravenClient;

    /**
     * @param \Df\Sentry\Client $ravenClient
     * @param int          $level       The minimum logging level at which this handler will be triggered
     * @param Boolean      $bubble      Whether the messages that are handled can bubble up the stack or not
     */
    function __construct(\Df\Sentry\Client $ravenClient)
    {
        $this->ravenClient = $ravenClient;
    }

	/**
	 * 2017-07-10
	 * @param int $code
	 * @param string $m
	 * @param string $file
	 * @param int $line
	 * @param array $context
	 * @return bool|mixed
	 */
    final function handleError($code, $m, $file = '', $line = 0, $context=[]) {
    	// 2017-07-10
    	// «Magento 2.1 php7.1 will not be supported due to mcrypt deprecation»
		// https://github.com/magento/magento2/issues/5880
		// [PHP 7.1] How to fix the «Function mcrypt_module_open() is deprecated» bug?
		// https://mage2.pro/t/2392
    	if (E_DEPRECATED !== $code || !df_contains($m, 'mcrypt') && !df_contains($m, 'mdecrypt')) {
			$this->ravenClient->breadcrumbs->record([
				'category' => 'error_reporting',
				'message' => $m,
				'level' => $this->ravenClient->translateSeverity($code),
				'data' => ['code' => $code, 'line' => $line, 'file' => $file]
			]);
		}
		return !$this->existingHandler ? false : call_user_func(
			$this->existingHandler, $code, $m, $file, $line, $context
		);
    }

    function install()
    {
        $this->existingHandler = set_error_handler(array($this, 'handleError'), E_ALL);
        return $this;
    }
}
