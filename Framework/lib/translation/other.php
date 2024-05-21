<?php
use Magento\Framework\Phrase as P;
/**
 * 2015-09-29
 * @used-by df_map_to_options_t()
 * @uses __()
 * @param string[] $s
 * @return string[]
 */
function df_translate_a(array $s,bool $now = false):array {
	$r = array_map('__', $s); /** @var string[] $r */
	return !$now ? $r : array_map('strval', $r);
}