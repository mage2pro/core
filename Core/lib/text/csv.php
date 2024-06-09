<?php
use Magento\Framework\File\Csv;
/**
 * 2015-02-07
 * 2020-08-13
 * 		df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * 		df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_csv()
 * @used-by df_oro_get_list()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 * @used-by \Dfe\CheckoutCom\Method::disableEvent()
 * @used-by \Dfe\FacebookLogin\Customer::r()
 * @param string|string[] ...$a
 */
function df_csv(...$a):string {return implode(',', df_args($a));}

/**
 * 2017-06-21
 * @used-by df_intl_dic_read()  
 * @used-by df_module_csv2()
 */
function df_csv_o():Csv {return df_new_om(Csv::class);}

/**
 * 2015-02-07
 * @used-by df_country_codes_allowed()
 * @used-by df_csv_parse_int()
 * @used-by df_days_off()
 * @used-by df_fe_fc_csv()
 * @used-by \BlushMe\Checkout\Block\Extra::items()
 * @used-by \Df\Config\Settings::csv()
 * @used-by \Df\Framework\Validator\Currency::__construct()
 * @used-by \Df\Payment\Method::amountFactor()
 * @used-by \Df\Payment\Method::canUseForCountryP()
 * @used-by \Dfe\CheckoutCom\Handler::isInitiatedByMyself()
 * @used-by \Dfe\CheckoutCom\Method::disableEvent()
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @param string|null $s
 * @return string[]
 */
function df_csv_parse($s, string $d = ','):array {return !$s ? [] : df_trim(explode($d, $s));}

/**
 * @used-by df_days_off()
 * @used-by \CabinetsBay\Catalog\B\Category::l3p() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/home.phtml (https://github.com/cabinetsbay/core/issues/8)
 * @param string|null $s
 * @return int[]
 */
function df_csv_parse_int($s):array {return df_int(df_csv_parse($s));}

/**
 * 2015-02-07
 * 2020-08-13
 * 		df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * 		df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_csv()
 * @used-by df_assert_in()
 * @used-by df_csv_pretty_quote()
 * @used-by df_oro_headers()
 * @used-by df_style_inline_hide()
 * @used-by dfe_modules_log()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Framework\Validator\Currency::message()
 * @used-by \Df\Sentry\Client::send()
 * @used-by \Dfe\Geo\Client::all()
 * @used-by \Dfe\Moip\P\Reg::ga()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param string|string[] ...$a
 */
function df_csv_pretty(...$a):string {return implode(', ', dfa_flatten($a));}

/**
 * 2022-10-29 @deprecated It is unused.
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @param string|string[] ...$a
 */
function df_csv_pretty_quote(...$a):string {return df_csv_pretty(df_quote_russian(df_args($a)));}