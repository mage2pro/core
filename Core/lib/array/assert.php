<?php
/**
 * 2023-07-26
 * 1) "Implement `dfa_assert_keys()`": https://github.com/mage2pro/core/issues/258
 * 2) @deprecated It is unused.
 */
function dfa_assert_keys(array $a, array $kk):void {
	foreach ($kk as $k) { /** @var string $k */
		if (!isset($a[$k])) {
			df_error("The required key «{$k}» is absent.\nThe array:\n" . df_dump($a));
		}
	}
}

/**
 * 2023-07-26 "Implement `dfa_has_keys()`": https://github.com/mage2pro/core/issues/258
 * @used-by df_caller_m()
 */
function dfa_has_keys(array $a, array $kk):bool {return count($kk) === count(dfa($a, $kk));}