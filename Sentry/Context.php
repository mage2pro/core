<?php
namespace Df\Sentry;
class Context
{
    public function __construct()
    {
        $this->clear();
    }

    /**
     * Clean up existing context.
     */
    public function clear()
    {
        $this->tags = array();
        $this->extra = array();
        $this->user = null;
    }
}
