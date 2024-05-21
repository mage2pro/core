<?php
use Magento\Framework\Phrase as P;
/**
 * 2024-05-22 "Implement `df_is_phrase()`": https://github.com/mage2pro/core/issues/381
 * @used-by df_check_s()
 * @used-by df_is_phrase_or_s()
 * @used-by df_phrase()
 * @used-by \Df\Core\Exception::__construct()
 * @used-by \Df\Payment\W\Responder::setSoftFailure()
 * @used-by \Df\Rss\Plugin\Model\Rss::afterGetFeeds()
 * @param mixed $v
 */
function df_is_phrase($v):bool {return $v instanceof P;}

/**
 * 2024-05-22 "Implement `df_is_phrase()`": https://github.com/mage2pro/core/issues/381
 * @used-by \Df\Core\Exception::__construct()
 * @param mixed $v
 */
function df_is_phrase_or_s($v):bool {return is_string($v) || df_is_phrase($v);}

/**
 * 2016-07-14
 * @used-by df_checkout_message()
 * @used-by df_message_add()
 * @param P|string $s
 */
function df_phrase($s):P {return df_is_phrase($s) ? $s : __($s);}