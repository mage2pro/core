<?php
use Magento\Directory\Model\Currency as C;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;

/**
 * 2015-12-28
 * @used-by df_currencies_ctn()
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return string[]
 */
function df_currencies_codes_allowed($s = null):array {return df_store($s)->getAvailableCurrencyCodes(true);}

/**
 * 2016-09-05
 * @used-by \Df\Directory\FE\Currency::map()
 * @used-by \Df\Payment\Currency::fromBase()
 * @used-by \PPCs\Core\Plugin\Iksanika\Stockmanage\Block\Adminhtml\Product\Grid::aroundAddColumn()
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 */
function df_currency_base_c($s = null):string {return df_currency_base($s)->getCode();}

/**
 * 2016-07-04       
 * @used-by df_currency_by_country_c()
 * @used-by df_currency_name()
 * @used-by df_currency_num()   
 * @param C|string|null $c [optional]
 */
function df_currency_code($c = null):string {return df_currency($c)->getCode();}

/**
 * 2016-09-05
 * В отличие от @see df_currency_base_с() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @used-by \Df\Directory\FE\Currency::map()
 * @param int|string|null|bool|StoreInterface $s [optional]
 */
function df_currency_current_c($s = null):string {return df_currency_current($s)->getCode();}

/**
 * 2018-09-26
 * It returns the currency's numeric ISO 4217 code:
 * https://en.wikipedia.org/wiki/ISO_4217#Active_codes
 * I use the database from the `sokil/php-isocodes` library:
 * https://github.com/sokil/php-isocodes/blob/8cd8c1f0/databases/iso_4217.json
 * @used-by \Dfe\TBCBank\Charge::common()
 * @used-by \Dfe\TBCBank\Facade\Charge::capturePreauthorized()
 * @param string|C|string[]|C[]|null $c
 */
function df_currency_num($c = null):string {return dfa(df_currency_nums(), df_currency_code($c));}

/**
 * 2018-09-26  
 * @used-by df_currency_num()
 * @return array(string => string)
 */
function df_currency_nums():array {return dfcf(function() {return array_column(
	df_module_json('Df_Directory', 'iso4217'), 'numeric', 'alpha'
);});}