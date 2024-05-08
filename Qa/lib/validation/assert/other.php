<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

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