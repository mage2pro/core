<?php
use Df\Core\Exception as DFE;
use Exception as E;

/**
 * @used-by df_vector_sum()
 * @param string|int|float|bool $expected
 * @param string|int|float|bool $v
 * @param string|E|null $m [optional]
 * @return string|int|float|bool
 * @throws DFE
 */
function df_assert_eq($expected, $v, $m = null) {return $expected === $v ? $v : df_error($m ?: sprintf(
	"Expected «%s», got «%s».", df_dump($expected), df_dump($v)
));}

/**
 * @used-by df_nat()
 * @param int|float $lowBound
 * @param int|float $v
 * @param string|E|null $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_ge($lowBound, $v, $m = null) {return $lowBound <= $v ? $v : df_error($m ?:
	"A number >= $lowBound is expected, but got $v."
);}

/**
 * 2017-01-15 В настоящее время никем не используется.
 * @param int|float $lowBound
 * @param int|float $v
 * @param string|E|null $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_gt($lowBound, $v, $m = null) {return $lowBound <= $v ? $v : df_error($m ?:
	"A number > $lowBound is expected, but got $v."
);}

/**
 * @used-by df_float_positive()
 * @used-by df_nat()
 * @used-by \Df\Customer\Settings\BillingAddress::restore()
 * @used-by \Dfe\CurrencyFormat\FE::onFormInitialized()
 * @param int|float|string $v
 * @param string|E|null $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_gt0($v, $m = null) {return 0 <= $v ? $v : df_error($m ?: "A positive number is expected, but got $v.");}

/**
 * @used-by \Mangoit\MediaclipHub\Model\Orders::byOId()
 * @param int|float $highBound
 * @param int|float $v
 * @param string|E|null $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_le($highBound, $v, $m = null) {return $highBound >= $v ? $v : df_error($m ?:
	"A number <= $highBound is expected, but got $v."
);}

/**
 * @used-by \RWCandy\Captcha\Assert::name()
 * @param int|float $highBound
 * @param int|float $v
 * @param string|E|null $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_lt($highBound, $v, $m = null) {return $highBound >= $v ? $v : df_error($m ?:
	"A number < $highBound is expected, but got $v."
);}

/**
 * @used-by df_action_name()
 * @used-by df_contents()
 * @used-by df_file_name()
 * @used-by df_http_get()
 * @used-by df_json_decode()
 * @used-by df_request_body()
 * @used-by \Df\Framework\Form\Element\ArrayT::onFormInitialized()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @param string|int|float|bool $neResult
 * @param string|int|float|bool $v
 * @param string|E|null $m [optional]
 * @return string|int|float|bool
 * @throws DFE
 */
function df_assert_ne($neResult, $v, $m = null) {return $neResult !== $v ? $v : df_error($m ?:
	"The value $v is rejected, any other is allowed."
);}