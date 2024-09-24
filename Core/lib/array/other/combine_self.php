<?php
/**
 * 2016-11-25
 * @used-by Df\Config\Source\SizeUnit::map()
 * @used-by Dfe\AmazonLogin\Source\Button\Native\Size::map()
 * @used-by Dfe\CheckoutCom\Source\Prefill::map()
 * @used-by Dfe\FacebookLogin\Source\Button\Size::map()
 * @used-by Dfe\ZohoCRM\Source\Domain::map()
 * @used-by KingPalm\B2B\Source\Type::map()
 * @used-by df_a_to_options()
 * @param string|int|int[]|string[] ...$a
 * @return array(int|string => int|string)
 */
function dfa_combine_self(...$a):array {$a = df_args($a); return array_combine($a, $a);}