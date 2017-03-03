<?php
namespace Df\Config;
/**
 * 2016-08-07
 * @see \Df\Config\Source\LetterCase
 * @see \Df\Config\Source\NoWhiteBlack
 * @see \Df\Config\Source\SizeUnit
 * @see \Df\Config\Source\Testable
 * @see \Df\GingerPaymentsBase\Source\Option
 * @see \Df\Payment\Metadata
 * @see \Df\Payment\Source\ACR
 * @see \Df\Sso\Source\Button\Type\UL
 * @see \Dfe\AllPay\Source\Option
 * @see \Dfe\AllPay\Source\OptionsLocation
 * @see \Dfe\AllPay\Source\PaymentIdentificationType
 * @see \Dfe\AllPay\Source\WaitPeriodType
 * @see \Dfe\AmazonLogin\Source\Button\Native\Color
 * @see \Dfe\AmazonLogin\Source\Button\Native\Size
 * @see \Dfe\AmazonLogin\Source\Button\Native\Type
 * @see \Dfe\CheckoutCom\Source\Action
 * @see \Dfe\CheckoutCom\Source\Prefill
 * @see \Dfe\FacebookLogin\Source\Button\Size
 * @see \Dfe\Omise\Source\Prefill
 * @see \Dfe\Paymill\Source\Prefill
 * @see \Dfe\SecurePay\Source\ForceResult
 * @see \Dfe\Spryng\Source\Prefill
 * @see \Dfe\Square\Source\Location
 * @see \Dfe\Stripe\Source\Prefill
 * @see \Dfe\TwoCheckout\Source\Prefill
 */
abstract class SourceT extends Source {
	/**
	 * 2016-08-07
	 * @used-by \Df\Payment\Settings\Options::o()
	 * @used-by \Df\GingerPaymentsBase\Source\Option::optionsTest()
	 * @param string[]|null $keys [optional]
	 * @return array(string => string)
	 */
	function options($keys = null) {
		/** @var array(string => string) $options */
		$options = $this->map();
		return df_translate_a(is_null($keys) ? $options : dfa_select_ordered($options, $keys));
	}

	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Config\Source::toOptionArray()
	 * @return array(array(string => string))
	 */
	function toOptionArray() {return df_map_to_options_t($this->map());}
}
