<?php
use Magento\Framework\Phrase as P;
/**
 * 2024-05-22 "Implement `df_is_phrase()`": https://github.com/mage2pro/core/issues/381
 * @param mixed $v
 */
function df_is_phrase($v):bool {return $v instanceof P;}

/**
 * 2016-07-14
 * @used-by df_checkout_message()
 * @used-by df_message_add()
 * @param P|string $s
 */
function df_phrase($s):P {return $s instanceof P ? $s : __($s);}