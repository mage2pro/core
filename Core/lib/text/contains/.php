<?php
/**
 * Я так понимаю, здесь безопасно использовать @uses strpos вместо @see mb_strpos() даже для UTF-8.
 * http://stackoverflow.com/questions/13913411/mb-strpos-vs-strpos-whats-the-difference
 * 2015-04-17 Добавлена возможность указывать в качестве $needle массив.
 * 2022-10-14 @see str_contains() has been added to PHP 8: https://php.net/manual/function.str-contains.php
 * 2022-11-26 We can not declare the argument as `string ...$n` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_block_output()
 * @used-by df_bt_filter_head()
 * @used-by df_is_bin_magento()
 * @used-by df_is_multiline()
 * @used-by df_request_ua()
 * @used-by df_rp_has()
 * @used-by ikf_ite()
 * @used-by mnr_recurring_is()
 * @used-by \Alignet\Paymecheckout\Plugin\Magento\Framework\Session\SidResolver::aroundGetSid() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \CanadaSatellite\Bambora\Facade::api() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Core\Plugin\Mageplaza\Blog\Helper\Data::afterGetBlogUrl() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/43)
 * @used-by \Df\Cron\Model\LoggerHandler::p()
 * @used-by \Df\Framework\Config\Dom\L::validate()
 * @used-by \Df\Sentry\Trace::get_frame_context()
 * @used-by \Df\Xml\G::k()
 * @used-by \Dfe\CurrencyFormat\Plugin\Catalog\Controller\Adminhtml\Product\Initialization\Helper\AttributeFilter::parse()
 * @used-by \DxMoto\Core\Observer\CanLog::execute()
 * @used-by \RWCandy\Captcha\Assert::name()
 * @used-by \TFC\Core\Observer\CanLog::execute()
 * @param string|string[] ...$n
 */
function df_contains(string $haystack, ...$n):bool {/** @var bool $r */
	# 2017-07-10 This branch is exclusively for optimization.
	# 2022-11-26 The previous (also correct) condition was: `1 === count($n) && !is_array($n0 = $n[0])`
	if (!is_array($n0 = df_arg($n))) {
		$r = false !== strpos($haystack, $n0);
	}
	else {
		$r = false;
		$n = dfa_flatten($n);
		foreach ($n as $nItem) {/** @var string $nItem */
			if (false !== strpos($haystack, $nItem)) {
				$r = true;
				break;
			}
		}
	}
	return $r;
}