<?php
use Magento\Directory\Model\Currency as C;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;

/**
 * 2016-07-04
 * «How to programmatically convert a money amount from a currency to another one?» https://mage2.pro/t/1842
 * 2016-09-05
 * Обратите внимание, что перевод из одной валюты в другую
 * надо осуществлять только в направлении 'базовая валюта' => 'второстепенная валюта',
 * но не наоборот
 * (Magento не умеет выполнять первод 'второстепенная валюта' => 'базовая валюта'
 * даже при наличии курса 'базовая валюта' => 'второстепенная валюта',
 * и возбуждает исключительную ситуацию).
 *
 * Курс валюты сау на себя в системе всегда есть:
 * @see \Magento\Directory\Model\ResourceModel\Currency::getRate()
 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Directory/Model/ResourceModel/Currency.php#L56-L58
 *
 * @uses \Magento\Directory\Model\Currency::convert() прекрасно понимает нулевой $to:
 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Directory/Model/Currency.php#L216-L217
 *
 * @used-by \Df\Payment\Currency::toBase()
 * @used-by \Df\Payment\Currency::toOrder()
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 * @param float $a
 * @param C|string|null $from [optional]
 * @param C|string|null $to [optional]
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 */
function df_currency_convert($a, $from = null, $to = null, $s = null):float {return df_currency_convert_from_base(
	df_currency_convert_to_base($a, $from, $s), $to, $s
);}

/**
 * 2017-04-15
 * @used-by \Dfe\Stripe\Method::minimumAmount()
 * @used-by \Dfe\TwoCheckout\Method::minimumAmount()
 * @param float $a
 * @param C|string|null $from [optional]
 * @param C|string|null $to [optional]
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 */
function df_currency_convert_safe($a, $from = null, $to = null, $s = null):float {return df_try(
	function() use($a, $from, $to, $s) {return df_currency_convert($a, $from, $to, $s);}, $a
);}

/**
 * 2016-09-05
 * @used-by df_oqi_price()
 * @param float $a
 * @param C|string|null $to
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 */
function df_currency_convert_from_base($a, $to, $s = null):float {return df_currency_base($s)->convert($a, $to);}

/**
 * 2016-09-05
 * @used-by df_currency_convert()
 * @used-by \Frugue\Shipping\Method::collectRates()
 * @param float $a
 * @param C|string|null $from
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 */
function df_currency_convert_to_base($a, $from, $s = null):float {return $a / df_currency_base($s)->convert(1, $from);}

/**
 * 2016-06-30
 * «How to programmatically check whether a currency is allowed and has an exchange rate to the base currency?»
 * https://mage2.pro/t/1832
 * @used-by \Df\Framework\Validator\Currency::check()
 * @param string $iso3
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return string[]
 */
function df_currency_has_rate($iso3, $s = null):array {return !!dfa(df_currencies_ctn($s), $iso3);}

/**
 * 2016-08-08
 * @used-by \Dfe\AllPay\ConfigProvider::config()
 */
function df_currency_rate_to_current():float {return df_currency_base()->getRate(df_currency_current());}