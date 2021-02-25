<?php
use Df\Core\Helper\Text as T;
use Magento\Framework\Phrase as P;
/**
 * 2015-11-22
 * @param string|string[]|P|P[] $text
 * @return string|string[]
 */
function df_quote_double($text) {return df_t()->quote($text, T::QUOTE__DOUBLE);}

/**
 * @param string|string[]|P|P[] $text
 * @return string|string[]
 */
function df_quote_russian($text) {return df_t()->quote($text, T::QUOTE__RUSSIAN);}

/**
 * @used-by df_ejs()
 * @param string|string[]|P|P[] $text
 * @return string|string[]
 */
function df_quote_single($text) {return df_t()->quote($text, T::QUOTE__SINGLE);}