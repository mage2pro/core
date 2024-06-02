<?php
use Df\Config\ArrayItem as AI;
use Df\Core\Exception as DFE;
use Magento\Framework\DataObject as _DO;

/**
 * @used-by df_con_hier_suf_ta()
 * @used-by df_explode_xpath()
 * @used-by df_fe_init()
 * @used-by df_find()
 * @used-by df_map()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
 * @used-by \TFC\Core\B\Home\Slider::p() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @param mixed|mixed[] $v
 */
function df_array($v):array {return is_array($v) ? $v : [$v];}

/**               
 * 2016-11-25
 * @used-by \Df\Config\Source\SizeUnit::map()
 * @used-by \Dfe\AmazonLogin\Source\Button\Native\Size::map()
 * @used-by \Dfe\CheckoutCom\Source\Prefill::map()
 * @used-by \Dfe\FacebookLogin\Source\Button\Size::map()
 * @used-by \Dfe\ZohoCRM\Source\Domain::map() 
 * @used-by \KingPalm\B2B\Source\Type::map()
 * @used-by df_a_to_options()
 * @param string|int|int[]|string[] ...$a
 * @return array(int|string => int|string)
 */
function dfa_combine_self(...$a):array {$a = df_args($a); return array_combine($a, $a);}


/**
 * Алгоритм взят отсюда: https://php.net/manual/function.array-unshift.php#106570
 * 2022-10-31 @deprecated It is unused.
 * @param array(string => mixed) $a
 * @param mixed $v
 */
function dfa_unshift_assoc(&$a, string $k, $v):void  {
	$a = array_reverse($a, true);
	$a[$k] = $v;
	$a = array_reverse($a, true);
}