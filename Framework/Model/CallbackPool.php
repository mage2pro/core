<?php
/**
 * 2016-05-29
 * I have duplicated it from @see \Magento\Framework\Model\CallbackPool
 * because the last one is absent in Magento 2 versions before 2.1 RC1:
 * https://mail.google.com/mail/u/0/#inbox/154f9e0eb03982aa
 */
namespace Df\Framework\Model;
class CallbackPool
{
    /**
     * Array of callbacks subscribed to commit transaction commit
     *
     * @var array
     */
    private static $commitCallbacks = [];

    /**
     * @param string $hashKey
     * @param array $callback
     * @return void
     */
    static function attach($hashKey, $callback)
    {
        self::$commitCallbacks[$hashKey][] = $callback;
    }

    /**
     * @param string $hashKey
     * @return void
     */
    static function clear($hashKey)
    {
        self::$commitCallbacks[$hashKey] = [];
    }

    /**
     * @param string $hashKey
     * @return array
     */
    static function get($hashKey)
    {
        if (!isset(self::$commitCallbacks[$hashKey])) {
            return [];
        }
        $callbacks = self::$commitCallbacks[$hashKey];
        self::$commitCallbacks[$hashKey] = [];
        return $callbacks;
    }
}
