<?php
use Df\Directory\Model\Country as C;
use Df\Directory\Model\ResourceModel\Country\Collection as CC;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2016-05-20
 * @used-by df_countries_ctn()
 * @used-by df_country()
 * @param bool $allowedOnly [optional]
 * @param int|string|null|bool|IStore $s [optional]
 * @return CC
 */
function df_countries($allowedOnly = false, $s = null) {return !$allowedOnly ? CC::s() : df_countries_allowed($s);}

/**
 * 2016-05-20
 * @param int|string|null|bool|IStore $s [optional]
 * @return CC
 */
function df_countries_allowed($s = null) {return dfcf(function($id) {return
	C::c()->loadByStore($id)
;}, [df_store_id($s)]);}

/**        
 * 2016-05-20
 * Возвращает массив,
 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
 * а значениями — названия стран для заданной локали (или системной локали по умолчанию).
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * Например:
 *	array(
 *		'AU' => 'Австралия'
 *		,'AT' => 'Австрия'
 *	)
 * @used-by df_countries_options()
 * @used-by df_country_ctn()
 * @param string|null $locale [optional]
 * @return array(string => string)
 */
function df_countries_ctn($locale = null) {return df_countries()->mapFromCodeToName($locale);}

/**
 * 2017-01-21
 * @used-by \Df\Directory\FE\Country::getValues()
 * В отличие от @see df_currencies_options(), здесь мы не используем параметр $store,
 * потому что пока мы используем нащу функцию не для получения списка стран,
 * доступных покупателю, а для получения списка стран, доступных магазину.
 * @param string[] $filter [optional]
 * @return array(array(string => string))
 */
function df_countries_options(array $filter = []) {return dfcf(function(array $filter = []) {return df_map_to_options(
	df_sort_names(dfa(df_countries_ctn(), df_etn($filter)))
);}, func_get_args());}

/**
 * 2016-05-20 It returns the country by its ISO 3166-1 alpha-2 code: https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
 * @used-by df_country_code()
 * @used-by df_store_country()
 * @param C|string $c
 * @param bool $throw [optional]
 * @return C|null
 */
function df_country($c, $throw = true) {return $c instanceof C ? $c : dfcf(function($iso2, $throw = true) {/** @var C|null $r */
	$r = !df_check_iso2($iso2) ? null : df_countries()->getItemById($iso2);
	return $r || !$throw ? $r : df_error("Unable to detect a country by the «{$iso2}» code.");
}, func_get_args());}

/**
 * 2016-05-20 Конвертирует 2-символьный код страны (например, «RU») в 3-символьный («RUS»).
 * @used-by \Dfe\TwoCheckout\Address::countryIso3()
 * @used-by \Dfe\Moip\P\Reg::pShippingAddress()
 * @used-by \Dfe\Moip\Test\Data::address()
 * @param string $iso2
 * @return string
 */
function df_country_2_to_3($iso2) {return df_result_sne(dfa(CC::s()->mapFrom2To3(), $iso2));}

/**
 * 2016-05-20 Конвертирует 3-символьный код страны (например, «RUS») в двухсимвольный («RU»).
 * @used-by \Dfe\Moip\Facade\Card::country()
 * @param string $iso3
 * @return string
 */
function df_country_3_to_2($iso3) {return df_result_sne(dfa(CC::s()->mapFrom3To2(), $iso3));}

/**
 * 2017-01-29
 * @param string|C $c
 * @return string
 */
function df_country_code($c) {return df_country($c)->getIso2Code();}

/**
 * 2015-12-28
 * @used-by \Df\Phone\Js::_toHtml()
 * @used-by \Dfe\Customer\Block::_toHtml()
 * @param int|string|null|bool|IStore $s [optional]
 * @return string[]
 */
function df_country_codes_allowed($s = null) {return df_csv_parse(df_cfg('general/country/allow', $s));}

/**        
 * 2016-05-20
 * It returns the country name name for an ISO 3166-1 alpha-2 2-characher code and locale
 * (or the default system locale) given: https://ru.wikipedia.org/wiki/ISO_3166-1
 * @used-by df_country_ctn_ru()
 * @used-by \Df\StripeClone\CardFormatter::country()
 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
 * @used-by \Dfe\IPay88\Block\Info::prepare()
 * @used-by \Dfe\Klarna\Test\Charge::t01()
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 * @param string $iso2
 * @param string|null $locale [optional]
 * @return string
 */
function df_country_ctn($iso2, $locale = null) {df_param_iso2($iso2, 0); return
	dfa(df_countries_ctn($locale), strtoupper($iso2)) ?: df_error(
		'Unable to find out name of the country with ISO code «%1» for locale «%2».',
		$iso2 ,df_locale($locale)
	)
;}

/** 
 * 2016-05-20
 * @uses df_country_ctn()
 * @param string $iso2
 * @return string
 */
function df_country_ctn_ru($iso2) {return df_country_ctn($iso2, 'ru_RU');}

/**
 * 2018-04-13
 * https://gist.github.com/henrik/1688572#gistcomment-2397203
 * https://github.com/mage2pro/frugue.com/issues/2
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Frugue\Core\Plugin\Directory\Model\Resource\Country\Collection::aroundLoadByStore()
 * @used-by \Frugue\Core\Plugin\Sales\Model\Quote::afterGetAddressesCollection()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @param string|null $c [optional]
 * @return string[]|bool
 */
function df_eu($c = null) {
	$r = [
		'AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'HR', 'IE',
		'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'
	];
	return !$c ? $r : in_array($c, $r);
}