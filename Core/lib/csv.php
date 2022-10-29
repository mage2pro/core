<?php
use Magento\Framework\File\Csv;
/**
 * 2015-02-07
 * 2020-08-13
 * df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * @see df_csv()
 * @used-by df_oro_get_list()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 * @used-by \Dfe\CheckoutCom\Method::disableEvent()
 * @used-by \Dfe\FacebookLogin\Customer::responseA()
 * @param string|string[] ...$args
 */
function df_csv(...$args):string {return implode(',', df_args($args));}

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
 * @param string $d [optional]
 * @return string[]
 */
function df_csv_parse($s, $d = ','):array {return !$s ? [] : df_trim(explode($d, $s));}

/**
 * @param string|null $s
 * @return int[]
 */
function df_csv_parse_int($s):array {return df_int(df_csv_parse($s));}

/**
 * 2015-02-07
 * 2020-08-13
 * df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * @see df_csv()
 * @used-by dfe_modules_log()
 * @used-by \Df\Sentry\Client::send()
 * @used-by \Dfe\Moip\P\Reg::ga()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param string|string[] ...$args
 */
function df_csv_pretty(...$args):string {return implode(', ', dfa_flatten($args));}

/**
 * 2022-10-29 @deprecated It is unused.
 * @param string|string[] ...$args
 */
function df_csv_pretty_quote(...$args):string {return df_csv_pretty(df_quote_russian(df_args($args)));}