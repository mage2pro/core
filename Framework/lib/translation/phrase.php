<?php
use Magento\Framework\Phrase as P;
/**
 * 2016-07-14
 * @used-by df_checkout_message()
 * @used-by df_message_add()
 * @param P|string $s
 */
function df_phrase($s):P {return $s instanceof P ? $s : __($s);}