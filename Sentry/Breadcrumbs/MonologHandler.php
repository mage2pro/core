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
     * @param \Df\Sentry\Client $ravenClient
     * @param int          $level       The minimum logging level at which this handler will be triggered
     * @param Boolean      $bubble      Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(\Df\Sentry\Client $ravenClient, $level = Logger::DEBUG, $bubble = true)
    {
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
