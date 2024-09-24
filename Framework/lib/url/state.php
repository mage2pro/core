<?php
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * http://mage2.ru/t/37
 * @used-by df_context()
 * @used-by df_locale()
 * @used-by df_replace_store_code_in_url()
 * @used-by df_store_code_from_url()
 * @used-by Df\OAuth\FE\Button::onFormInitialized()
 * @used-by Df\Sentry\Client::get_http_data()
 * @used-by Dfe\BlackbaudNetCommunity\Url::get()
 */
function df_current_url():string {return df_url_o()->getCurrentUrl();}

/**
 * 2016-03-09
 * I have ported it from my «Russian Magento» product for Magento 1.x: http://magento-forum.ru
 * @uses df_store_url_web() returns an empty string
 * if the store's root URL is absent in the Magento database.
 * 2017-03-15
 * It returns null only if the both conditions are true:
 * 1) Magento runs from the command line (by Cron or in console).
 * 2) The store's root URL is absent in the Magento database.
 * @used-by df_sentry()
 * @used-by dfe_modules_log()
 * @used-by dfp_refund()
 * @used-by Df\Payment\Metadata::vars()
 * @used-by Dfe\Dynamics365\API\Client::headers()
 * @used-by Dfe\Vantiv\Charge::pCharge()
 * @param int|string|null|bool|IStore $s [optional]
 * @return string|null
 */
function df_domain_current($s = null, bool $www = false) {return dfcf(function($s = null, bool $www = false) {return
	!($base = df_store_url_web($s)) || !($r = df_domain($base, false)) ? null : (
		$www ? $r : df_trim_text_left($r, 'www.')
	)
;}, func_get_args());}