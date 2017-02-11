<?php
namespace Df\Sentry;
class Breadcrumbs
{
    function __construct($size = 100)
    {
        $this->count = 0;
        $this->pos = 0;
        $this->size = $size;
        $this->buffer = [];
    }

    function record($crumb)
    {
        if (empty($crumb['timestamp'])) {
            $crumb['timestamp'] = microtime(true);
        }
        $this->buffer[$this->pos] = $crumb;
        $this->pos = ($this->pos + 1) % $this->size;
        $this->count++;
    }

    function fetch()
    {
        $results = [];
        for ($i = 0; $i <= ($this->size - 1); $i++) {
            $idx = ($this->pos + $i) % $this->size;
            if (isset($this->buffer[$idx])) {
                $results[] = $this->buffer[$idx];
            }
        }
        return $results;
    }

    function is_empty()
    {
        return $this->count === 0;
    }

    function to_json()
    {
        return array(
            'values' => $this->fetch(),
        );
    }
}
