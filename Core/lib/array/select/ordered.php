<?php
/**
 * 2015-02-08
 * 2020-01-29
 * 1) It returns a subset of $a with $k keys in the same order as in $k.
 * 2) Normally, you should use @see dfa() instead because it is shorter and calls dfa_select_ordered() internally.
 * @used-by dfa()
 * @param array(string => string)|T $a
 * @param string[] $k
 * @return array(string => string)
 */
function dfa_select_ordered($a, array $k):array {
	$resultKeys = array_fill_keys($k, null); /** @var array(string => null) $resultKeys */
	/**
	 * 2017-10-28
	 * During the last 2.5 years, I had the following code here:
	 * 		array_merge($resultKeys, df_ita($source))
	 * It worked wronly, if $source contained SOME numeric-string keys like "99":
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
	 *
	 * «A key may be either an integer or a string.
	 * If a key is the standard representation of an integer, it will be interpreted as such
	 * (i.e. "8" will be interpreted as 8, while "08" will be interpreted as "08").»
	 * https://php.net/manual/language.types.array.php
	 *
	 * «If, however, the arrays contain numeric keys, the later value will not overwrite the original value,
	 * but will be appended.
	 * Values in the input array with numeric keys will be renumbered
	 * with incrementing keys starting from zero in the result array.»
	 * https://php.net/manual/function.array-merge.php
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140297
	 * `df_ita($source) + $resultKeys` does not solve the problem,
	 * because the result keys are ordered in the `$source` order, not in the `$resultKeys` order:
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140766
	 * @var array(string => string) $resultWithGarbage
	 */
	$resultWithGarbage = dfa_merge_numeric($resultKeys, df_ita($a));
	/**
	 * 2023-07-20
	 * 1) Today I found a bug (at least, in PHP 8.2) in this old function.
	 * 		$a: {"EUR": "Euro", "USD": "US Dollar"}
	 * 		$k: ["CAD", "PHP", "USD"]
	 * 		Result (wrong because it is not a subset of $a): {"CAD": null, "PHP": null, "USD": "US Dollar"}
	 * 2) It led to the error: «df_option(): Argument #2 ($l) must be of type string, null given»:
	 * https://github.com/mage2pro/core/issues/238
	 * 3) So I have added @uses df_clean_null()
	 */
	return df_clean_null(array_intersect_key($resultWithGarbage, $resultKeys));
}