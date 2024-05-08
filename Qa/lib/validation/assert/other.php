<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2021-03-22
 * @used-by df_date_create()
 * @param int|float $v
 * @param int|float|null $min
 * @param int|float|null $max
 * @return int|float
 * @throws DFE
 */
function df_assert_between($v, $min, $max, bool $inclusive = true) {
	if (!df_between($v, $min, $max, $inclusive)) {
		list($o1, $o2) = !$inclusive ? ['>', '<'] : ['≥', '≤']; /** @var string $o1 */ /** @var string $o2 */
		df_error("The value «{$v}» is not allowed. An allowed value should be $o1 $min and $o2 $max.");
	}
	return $v;
}

/**
 * 2017-01-14 Отныне функция возвращает $v: это позволяет нам значительно сократить код вызова функции.
 * @used-by df_date_from_timestamp_14()
 * @used-by \Dfe\Zoho\App::title()
 * @used-by \Dfe\Omise\W\Event\Charge\Complete::isPending()
 * @param string|float|int|bool|null $v
 * @param array(string|float|int|bool|null) $a
 * @param string|Th $m [optional]
 * @return string|float|int|bool|null
 * @throws DFE
 */
function df_assert_in($v, array $a, $m = null) {
	if (!in_array($v, $a, true)) {
		df_error($m ?: "The value «{$v}» is rejected" . (
			10 >= count($a)
				? sprintf(". Allowed values: «%s».", df_csv_pretty($a))
				: " because it is absent in the list of allowed values."
		));
	}
	return $v;
}

/**
 * 2017-01-14       
 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::box()
 * @used-by \Dfe\GoogleFont\Fonts\Png::colorAllocateAlpha()
 * @used-by \Dfe\GoogleFont\Fonts\Png::image()
 * @used-by \Dfe\GoogleFont\Fonts\Sprite::draw()
 * @used-by \Df\Xml\X::asXMLPart()
 * @param mixed $v
 * @param string|Th $m [optional]
 * @return mixed
 * @throws DFE
 */
function df_assert_nef($v, $m = null) {return false !== $v ? $v : df_error($m ?:
	'The «false» value is rejected, any others are allowed.'
);}

/**
 * @used-by df_currency_base()
 * @used-by df_file_name()
 * @used-by df_json_decode()
 * @used-by \CanadaSatellite\Bambora\Action\Authorize::p() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Action\_Void::p() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \Df\Payment\W\Event::pid()
 * @used-by \Df\PaypalClone\Charge::p()
 * @used-by \Df\StripeClone\Payer::newCard()
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Dfe\Stripe\Controller\CustomerReturn\Index::isSuccess()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @throws DFE
 */
function df_assert_sne(string $v, int $sl = 0):string {
	$sl++;
	# The previous code `if (!$v)` was wrong because it rejected the '0' string.
	return !df_es($v) ? $v : Q::raiseErrorVariable(__FUNCTION__, [Q::NES], $sl);
}

/**
 * 2016-08-09
 * @used-by dfaf()
 * @param Traversable|array $v
 * @param string|Th $m [optional]
 * @return Traversable|array
 * @throws DFE
 */
function df_assert_traversable($v, $m = null) {return is_iterable($v) ? $v : df_error($m ?:
	'A variable is expected to be a Traversable or an array, ' . 'but actually it is %s.', df_type($v)
);}

/**
 * @used-by \Df\Config\Settings::b()
 * @used-by \Df\Framework\Form\Element\Checkbox::b()
 * @used-by \Df\Payment\Comment\Description::getCommentText()
 * @used-by \Df\Payment\Comment\Description::locations()
 * @used-by \Df\Shipping\Settings::enable()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @param mixed $v
 */
function df_bool($v):bool {
	/**
	 * Unfortunately, we can not replace @uses in_array() with @see array_flip() + @see isset() to speedup the execution,
	 * because it could lead to the warning: «Warning: array_flip(): Can only flip STRING and INTEGER values!».
	 * Moreover, @see array_flip() + @see isset() fails the following test:
	 *	$a = array(null => 3, 0 => 4, false => 5);
	 *	$this->assertNotEquals($a[0], $a[false]);
	 * Though, @see array_flip() + @see isset() does not fail the tests:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	static $no = [0, '0', 'false', false, null, 'нет', 'no', 'off', '']; /** @var mixed[] $no */
	static $yes = [1, '1', 'true', true, 'да', 'yes', 'on']; /** @var mixed[] $yes */
	/**
	 * Passing $strict = true to the @uses in_array() call is required here,
	 * otherwise any true-compatible value (e.g., a non-empty string) will pass the check.
	 */
	return in_array($v, $no, true) ? false : (in_array($v, $yes, true) ? true : df_error(
		'A boolean value is expected, but got «%s».', df_dump($v)
	));
}