<?php
/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_starts_with()
 */
function df_ends_with($haystack, $needle) {
	/** @var int $length */
	$length = mb_strlen($needle);
	return (0 === $length) || ($needle === mb_substr($haystack, -$length));
}

/**
 * Утверждают, что код ниже работает быстрее, чем return 0 === mb_strpos($haystack, $needle);
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_ends_with()
 * @used-by df_action_prefix()
 * @used-by df_check_https()
 * @used-by df_check_json_complex()
 * @used-by df_check_url_absolute()
 * @used-by df_check_xml()
 * @used-by df_handle_prefix()
 * @used-by df_log_l()
 * @used-by df_modules_p()
 * @used-by df_package()
 * @used-by df_path_is_internal()
 * @used-by df_prepend()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\Core\Helper\Text::isRegex()
 * @used-by \Df\Core\T\lib\csv::t01()
 * @used-by \Df\Framework\Form\Element::getClassDfOnly()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
 * @used-by \Df\Framework\Plugin\View\Layout::afterIsCacheable()
 * @used-by \Df\Framework\Request::extraKeysRaw()
 * @used-by \Df\OAuth\ReturnT::redirectUrl()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @used-by \Df\Payment\Observer\Multishipping::execute()
 * @used-by \Df\Payment\Source\API\Key\Testable::_test()
 * @used-by \Df\Payment\TID::i2e()
 * @used-by \Df\Qa\Message\Failure::states()
 * @used-by \Df\StripeClone\Facade\Charge::tokenIsNew()
 * @used-by \Df\Webapi\Plugin\Model\ServiceMetadata::aroundGetServiceName()
 * @used-by \Df\Zf\Validate\StringT\IntT::isValid()
 * @used-by \Dfe\Dynamics365\T\Basic::products()
 * @used-by \Dfe\Stripe\Facade\Token::isCard()
 * @used-by \Dfe\Stripe\Facade\Token::isPreviouslyUsedOrTrimmedSource()
 * @used-by \Dfr\Core\Realtime\Dictionary::_continue()
 * @param string $haystack
 * @param string|string[] $needle
 * @return bool
 */
function df_starts_with($haystack, $needle) {
	/** @var bool $result */
	if (!is_array($needle)) {
		$result = $needle === mb_substr($haystack, 0, mb_strlen($needle));
	}
	else {
		$result = false;
		foreach ($needle as $n) {
			/** @var string $n */
			if (df_starts_with($haystack, $n)) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}