<?php
namespace Df\Sentry;
abstract class Processor
{
    function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process and sanitize data, modifying the existing value if necessary.
     *
     * @param array $data   Array of log data
     */
    abstract function process(&$data);
}
