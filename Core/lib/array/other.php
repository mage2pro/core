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
 * 2015-12-30 Преобразует коллекцию или массив в карту.
 * @used-by df_mvars()
 * @used-by \Df\Config\A::get()
 * @param string|Closure $k
 * @param Traversable|array(int|string => _DO) $a
 */
function df_index($k, $a):array {return array_combine(df_column($a, $k), df_ita($a));}

/**
 * 2016-09-07
 * 2017-03-06 @uses mb_substr() корректно работает с $length = null.
 * 2022-11-23
 * If $length is 0, then @uses mb_substr() returns an empty string: https://3v4l.org/ijD3V
 * If $length is NULL, then @uses mb_substr() returns all characters to the end of the string.
 * https://3v4l.org/ijD3V
 * 2022-11-26 That is why I use @uses df_etn().
 * @used-by \Df\Payment\Charge::metadata()
 * @param string[] $a
 * @return string[]
 */
function dfa_chop(array $a, int $length = 0):array {return df_map('mb_substr', $a, [0, df_etn($length)]);}

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
 * Эта функция отличается от @uses array_fill() только тем,
 * что разрешает параметру $length быть равным нулю.
 * Если $length = 0, то функция возвращает пустой массив.
 * @uses array_fill() разрешает параметру $num (аналог $length)
 * быть равным нулю только начиная с PHP 5.6:
 * https://php.net/manual/function.array-fill.php
 * «5.6.0	num may now be zero. Previously, num was required to be greater than zero»
 * @see array_fill_keys()
 * @used-by df_vector_sum()
 * @param mixed $v
 */
function dfa_fill(int $startIndex, int $length, $v):array {return !$length ? [] : array_fill($startIndex, $length, $v);}

/**
 * 2016-07-31
 * Возвращает повторяющиеся элементы исходного массива (не повторяя их). https://3v4l.org/YEf5r
 * В алгоритме пользуемся тем, что @uses array_unique() сохраняет ключи исходного массива.
 * 2020-01-29 dfa_repeated([1,2,2,2,2,3,3,3,4]) => [2,3]
 * 2022-10-15
 * 1) The example above correctly works in 7.2 ≥ PHP ≤ 8.2: https://3v4l.org/Ds194
 * 2) If flags is @see SORT_STRING (it is by default),
 * formerly array has been copied and non-unique elements have been removed (without packing the array afterwards),
 * but now a new array is built by adding the unique elements. This can result in different numeric indexes.
 * https://php.net/manual/function.array-unique.php#refsect1-function.array-unique-changelog
 * @used-by \Df\Config\Backend\ArrayT::processI()
 */
function dfa_repeated(array $a):array {return array_values(array_unique(array_diff_key($a, array_unique($a))));}

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

/**
 * 2016-09-05
 * 2022-11-27 Added the df_nes() check.
 * @see dfa_strict()
 * @used-by df_cfg_save()
 * @used-by df_url_bp()
 * @used-by ikf_pw_country()
 * @used-by \Df\Directory\FE\Currency::v()
 * @used-by \Dfe\GingerPaymentsBase\Block\Info::prepareCommon()
 * @used-by \Dfe\GingerPaymentsBase\Choice::title()
 * @used-by \Dfe\GingerPaymentsBase\Method::optionE()
 * @used-by \Dfe\GingerPaymentsBase\Method::optionI()
 * @used-by \Df\Payment\BankCardNetworkDetector::label()
 * @used-by \Df\PaypalClone\W\Event::statusT()
 * @used-by \Dfe\AllPay\W\Reader::te2i()
 * @used-by \Dfe\IPay88\W\Event::optionTitle()
 * @used-by \Dfe\Moip\Facade\Card::brand()
 * @used-by \Dfe\Moip\Facade\Card::logoId()
 * @used-by \Dfe\Moip\Facade\Card::numberLength()
 * @used-by \Dfe\Paymill\Facade\Card::brand()
 * @used-by \Dfe\PostFinance\W\Event::optionTitle()
 * @used-by \Dfe\Robokassa\W\Event::optionTitle()
 * @used-by \Dfe\Square\Facade\Card::brand()
 * @used-by \Dfe\Stripe\FE\Currency::getComment()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Dfe\Vantiv\Facade\Card::brandCodeE()
 * @used-by \Frugue\Store\Block\Switcher::map()
 * @used-by \Frugue\Store\Block\Switcher::name()
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function dftr($v, array $map) {return df_nes($v) ? $v : dfa($map, $v, $v);}