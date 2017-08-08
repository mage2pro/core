<?php
namespace Df\Sentry\Breadcrumbs;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
class MonologHandler extends AbstractProcessingHandler
{
    /**
     * Translates Monolog log levels to Raven log levels.
     */
    private $logLevels = array(
        Logger::DEBUG     => \Df\Sentry\Client::DEBUG,
        Logger::INFO      => \Df\Sentry\Client::INFO,
        Logger::NOTICE    => \Df\Sentry\Client::INFO,
        Logger::WARNING   => \Df\Sentry\Client::WARNING,
        Logger::ERROR     => \Df\Sentry\Client::ERROR,
        Logger::CRITICAL  => \Df\Sentry\Client::FATAL,
        Logger::ALERT     => \Df\Sentry\Client::FATAL,
        Logger::EMERGENCY => \Df\Sentry\Client::FATAL,
    );

    private $excMatch = '/^exception \'([^\']+)\' with message \'(.+)\' in .+$/s';

    /**
     * @var \Df\Sentry\Client the client object that sends the message to the server
     */
    protected $ravenClient;

    /**
	 * 2017-08-09
	 * @override
	 * @see \Monolog\Handler\AbstractHandler::__construct()
     * @param \Df\Sentry\Client $ravenClient
     * @param int $level [optional] The minimum logging level at which this handler will be triggered
	 * 2017-08-09
	 * Unfortunately, we are unable to specify the type of the $bubble argument
	 * because of the Magento 2 compilation bug:
	 * «Df\Sentry\Breadcrumbs\MonologHandler: Incompatible argument type:
	 * Required type: \Monolog\Handler\Boolean. Actual type: \Df\Sentry\Breadcrumbs\Boolean».
	 * https://github.com/mage2pro/core/issues/19
	 * We are also unable to specify the `bool` type, it will lead to a similar failure:
	 * «Incompatible argument type: Required type: \Monolog\Handler\Boolean. Actual type: bool;»
	 * It is because @see \Monolog\Handler\AbstractHandler::__construct()
	 * specifies the $bubble argument type as `Boolean`:
	 * https://github.com/Seldaek/monolog/blob/1.23.0/src/Monolog/Handler/AbstractHandler.php#L36
	 * Actually, there is no @see \Monolog\Handler\Boolean class
     * @param \Monolog\Handler\Boolean|bool $bubble [optional]
	 * Whether the messages that are handled can bubble up the stack or not
     */
    function __construct(\Df\Sentry\Client $ravenClient, $level = Logger::DEBUG, $bubble = true) {
        parent::__construct($level, $bubble);
        $this->ravenClient = $ravenClient;
    }

    protected function parseException($message)
    {
        if (!preg_match($this->excMatch, $message, $matches)) {
            return;
        }

        return array($matches[1], $matches[2]);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        // sentry uses the 'nobreadcrumb' attribute to skip reporting
        if (!empty($record['context']['nobreadcrumb'])) {
            return;
        }

        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Exception) {
            $exc = $record['context']['exception'];
            $crumb = array(
                'type' => 'error',
                'level' => $this->logLevels[$record['level']],
                'category' => $record['channel'],
                'data' => array(
                    'type' => get_class($exc),
                    'value' => $exc->getMessage(),
                ),
            );
        } else {
            // TODO(dcramer): parse exceptions out of messages and format as above
            if ($error = $this->parseException($record['message'])) {
                $crumb = array(
                    'type' => 'error',
                    'level' => $this->logLevels[$record['level']],
                    'category' => $record['channel'],
                    'data' => array(
                        'type' => $error[0],
                        'value' => $error[1],
                    ),
                );
            } else {
                $crumb = array(
                    'level' => $this->logLevels[$record['level']],
                    'category' => $record['channel'],
                    'message' => $record['message'],
                );
            }
        }

        $this->ravenClient->breadcrumbs->record($crumb);
    }
}
