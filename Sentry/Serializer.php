<?php
namespace Df\Sentry;
/**
 * This helper is based on code from Facebook's Phabricator project
 *
 *   https://github.com/facebook/phabricator
 *
 * Specifically, it is an adaptation of the PhutilReadableSerializer class.
 *
 * @package raven
 */
class Serializer
{
    /*
     * The default mb detect order
     *
     * @see http://php.net/manual/en/function.mb-detect-encoding.php
     */
    const DEFAULT_MB_DETECT_ORDER = 'auto';

    /*
     * Suggested detect order for western countries
     */
    const WESTERN_MB_DETECT_ORDER = 'UTF-8, ASCII, ISO-8859-1, ISO-8859-2, ISO-8859-3, ISO-8859-4, ISO-8859-5, ISO-8859-6, ISO-8859-7, ISO-8859-8, ISO-8859-9, ISO-8859-10, ISO-8859-13, ISO-8859-14, ISO-8859-15, ISO-8859-16, Windows-1251, Windows-1252, Windows-1254';

    /**
     * This is the default mb detect order for the detection of encoding
     *
     * @var string
     */
    private $mb_detect_order= self::DEFAULT_MB_DETECT_ORDER;

    /**
     * @param null|string $mb_detect_order
     */
    function __construct($mb_detect_order = null)
    {
        if ($mb_detect_order != null) {
            $this->mb_detect_order = $mb_detect_order;
        }
    }
    /**
     * Serialize an object (recursively) into something safe for data
     * sanitization and encoding.
     */
    function serialize($value, $max_depth=3, $_depth=0)
    {
        $className = is_object($value) ? get_class($value) : null;
        $toArray = is_array($value) || $className === 'stdClass';
        if ($toArray && $_depth < $max_depth) {
            $new = [];
            foreach ($value as $k => $v) {
                $new[$this->serializeValue($k)] = $this->serialize($v, $max_depth, $_depth + 1);
            }

            return $new;
        }
        return $this->serializeValue($value);
    }

    protected function serializeString($value)
    {
        $value = (string) $value;
        if (function_exists('mb_detect_encoding')
            && function_exists('mb_convert_encoding')
        ) {
            // we always guarantee this is coerced, even if we can't detect encoding
            if ($currentEncoding = mb_detect_encoding($value, $this->mb_detect_order)) {
                $value = mb_convert_encoding($value, 'UTF-8', $currentEncoding);
            } else {
                $value = mb_convert_encoding($value, 'UTF-8');
            }
        }

        if (strlen($value) > 1024) {
            $value = substr($value, 0, 1014) . ' {clipped}';
        }

        return $value;
    }

    protected function serializeValue($value)
    {
        if (is_null($value) || is_bool($value) || is_float($value) || is_integer($value)) {
            return $value;
        } elseif (is_object($value) || gettype($value) == 'object') {
            return 'Object '.get_class($value);
        } elseif (is_resource($value)) {
            return 'Resource '.get_resource_type($value);
        } elseif (is_array($value)) {
            return 'Array of length ' . count($value);
        } else {
            return $this->serializeString($value);
        }
    }


    /**
     * @return string
     */
    function getMbDetectOrder()
    {
        return $this->mb_detect_order;
    }

    /**
     * @param string $mb_detect_order
     *
     * @return \Df\Sentry\Serializer
     */
    function setMbDetectOrder($mb_detect_order)
    {
        $this->mb_detect_order = $mb_detect_order;

        return $this;
    }
}
