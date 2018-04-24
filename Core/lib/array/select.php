<?php
/**
 * 2015-02-11
 * Из ассоциативного массива $source выбирает элементы с ключами $keys.
 * В отличие от @see dfa_select_ordered() не учитывает порядок ключей $keys
 * и поэтому работает быстрее, чем @see dfa_select_ordered().
 * @param array(string => string)|\Traversable $source
 * @param string[] $keys
 * @return array(string => string)
 */
function dfa_select($source, array $keys)  {return
	array_intersect_key(df_ita($source), array_fill_keys($keys, null))
;}

/**
 * 2015-02-08
 * Из ассоциативного массива $source выбирает элементы с ключами $orderedKeys
 * и возвращает их в том же порядке, в каком они перечислены в $orderedKeys.
 * Если порядок ключей не важен, но используйте более быстрый аналог @see dfa_select().
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @used-by \Dfe\Dragonpay\Signer\Request::values()
 * @used-by \Dfe\IPay88\Signer\Request::values()
 * @used-by \Dfe\IPay88\Signer\Response::values()
 * @used-by \Dfe\Robokassa\Signer\Request::values()
 * @used-by \Dfe\Robokassa\Signer\Response::values()
 * @param array(string => string)|\Traversable $source
 * @param string[] $orderedKeys
 * @return array(string => string)
 */
function dfa_select_ordered($source, array $orderedKeys)  {
	$resultKeys = array_fill_keys($orderedKeys, null); /** @var array(string => null) $resultKeys */
	/**
	 * 2017-10-28
	 * Previously, I had the following code here during 2.5 years:
	 * 		array_merge($resultKeys, df_ita($source))
	 * It works wronly, if $source contains SOME numeric-string keys like "99":
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
	 *
	 * «A key may be either an integer or a string.
	 * If a key is the standard representation of an integer, it will be interpreted as such
	 * (i.e. "8" will be interpreted as 8, while "08" will be interpreted as "08").»
	 * http://php.net/manual/en/language.types.array.php
	 *
	 * «If, however, the arrays contain numeric keys, the later value will not overwrite the original value,
	 * but will be appended.
	 * Values in the input array with numeric keys will be renumbered
	 * with incrementing keys starting from zero in the result array.»
	 * http://php.net/manual/en/function.array-merge.php
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140297
	 * `df_ita($source) + $resultKeys` does not solve the problem,
	 * because the result keys are ordered in the `$source` order, not in the `$resultKeys` order:
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140766
	 * @var array(string => string) $resultWithGarbage
	 */
	$resultWithGarbage = dfa_merge_numeric($resultKeys, df_ita($source));
	return array_intersect_key($resultWithGarbage, $resultKeys);
}