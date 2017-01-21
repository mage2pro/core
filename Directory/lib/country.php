<?php
use Df\Directory\Model\Country as C;
use Df\Directory\Model\ResourceModel\Country\Collection as CC;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2016-05-20
 * @param bool $allowedOnly [optional]
 * @param int|string|null|bool|IStore $store [optional]
 * @return CC
 */
function df_countries($allowedOnly = false, $store = null) {return
	!$allowedOnly ? CC::s() : df_countries_allowed($store)
;}

/**
 * 2016-05-20
 * @param int|string|null|bool|IStore $store [optional]
 * @return CC
 */
function df_countries_allowed($store = null) {return dfcf(function($store = null) {return
	C::c()->loadByStore(df_store_id($store))
;}, func_get_args());}

/**        
 * 2016-05-20
 * Возвращает массив,
 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
 * а значениями — названия стран для заданной локали (или системной локали по умолчанию).
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * Например:
	array(
		'AU' => 'Австралия'
 		,'AT' => 'Австрия'
	)
 * @param string|null $locale [optional]
 * @return array(string => string)
 */
function df_countries_ctn($locale = null) {return df_countries()->mapFromCodeToName($locale);}

/**          
 * 2016-05-20
 * @uses df_countries_ctn()
 * @return array(string => string)
 */
function df_countries_ctn_ru() {return df_countries_ctn('ru_RU');}

/**           
 * 2016-05-20
 * Возвращает массив,
 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
 * а значениями — названия стран в верхнем регистре для заданной локали
 * (или системной локали по умолчанию).
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * Например:
	array(
		'AU' => 'АВСТРАЛИЯ'
 		,'AT' => 'АВСТРИЯ'
	)
 * @param string|null $locale [optional]
 * @return array(string => string)
 */
function df_countries_ctn_uc($locale = null) {return df_countries()->mapFromCodeToNameUc($locale);}

/**         
 * 2016-05-20
 * @uses df_countries_ctn_uc()
 * @return array(string => string)
 */
function df_countries_ctn_uc_ru() {return df_countries_ctn_uc('ru_RU');}

/**
 * 2016-05-20
 * Возвращает массив,
 * в котором ключами являются
 * названия стран для заданной локали (или системной локали по умолчанию)
 * а значениями — 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2.
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * Например:
	array(
		'Австралия' => 'AU'
 		,'Австрия' => 'AT'
	)
 * @param string|null $locale [optional]
 * @return array(string => string)
 */
function df_countries_ntc($locale = null) {return df_countries()->mapFromNameToCode($locale);}

/**          
 * 2016-05-20
 * @uses df_countries_ntc()
 * @return array(string => string)
 */
function df_countries_ntc_ru() {return df_countries_ntc('ru_RU');}

/**      
 * 2016-05-20
 * Возвращает массив,
 * в котором ключами являются
 * названия стран в верхнем регистре для заданной локали (или системной локали по умолчанию)
 * а значениями — 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2.
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * Например:
	array(
		'АВСТРАЛИЯ' => 'AU'
 		,'АВСТРИЯ' => 'AT'
	)
 * @param string|null $locale [optional]
 * @return array(string => string)
 */
function df_countries_ntc_uc($locale = null) {return df_countries()->mapFromNameToCodeUc($locale);}

/**           
 * 2016-05-20
 * @uses df_countries_ntc_uc()
 * @return array(string => string)
 */
function df_countries_ntc_uc_ru() {return df_countries_ntc_uc('ru_RU');}

/**
 * 2017-01-21
 * В отличие от @see df_currencies_options(), здесь мы не используем параметр $store,
 * потому что пока мы используем нащу функцию не для получения списка стран,
 * доступных покупателю, а для получения списка стран, доступных магазину.
 * @param string[] $filter [optional]
 * @return array(array(string => string))
 */
function df_countries_options(array $filter = []) {return dfcf(function(array $filter = []) {
	/** @var array(string => string) $all */
	$all = df_countries_ctn();
	/**
	 * 2017-01-21
	 * Пока намеренно используем здесь @see dfa_select(), а не @see dfa_select_ordered(),
	 * потому что нам предпочительнее, чтобы страны были расположены в алфавитном порядке.
	 */
	return df_map_to_options(!$filter ? $all : dfa_select($all, $filter));
}, func_get_args());}

/**
 * 2016-05-20
 * Возвращает страну по её 2-буквенному коду по стандарту ISO 3166-1 alpha-2.
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * @param string $iso2
 * @param bool $throw [optional]
 * @return C|null
 */
function df_country($iso2, $throw = true) {return dfcf(function($iso2, $throw = true) {
	/** @var C|null $result */
	$result = !df_check_iso2($iso2) ? null : df_countries()->getItemById($iso2);
	if ($result) {
		df_assert($result instanceof C);
	}
	else if ($throw) {
		df_error('Не могу найти страну по 2-буквенному коду «%s».', $iso2);
	}
	return $result;
}, func_get_args());}

/**
 * 2016-05-20
 * Конвертирует 2-символьный код страны (например, «RU») в 3-символьный («RUS»).
 * @param string $iso2
 * @return string
 */
function df_country_2_to_3($iso2) {return df_result_sne(dfa(CC::s()->mapFrom2To3(), $iso2));}

/**
 * 2016-05-20
 * Конвертирует 3-символьный код страны (например, «RUS») в двухсимвольный («RU»).
 * @param string $iso3
 * @return string
 */
function df_country_3_to_2($iso3) {return df_result_sne(dfa(CC::s()->mapFrom3To2(), $iso3));}

/**
 * 2015-12-28
 * @param int|string|null|bool|IStore $store [optional]
 * @return string[]
 */
function df_country_codes_allowed($store = null) {return
	df_csv_parse(df_cfg('general/country/allow', $store))
;}

/**        
 * 2016-05-20
 * Возвращает название страны для заданной локали (или системной локали по умолчанию)
 * по 2-буквенному коду по стандарту ISO 3166-1 alpha-2.
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * @param string $iso2
 * @param string|null $locale [optional]
 * @return string
 */
function df_country_ctn($iso2, $locale = null) {
	df_param_iso2($iso2, 0);
	/** @var string $result */
	$result = dfa(df_countries_ctn($locale), $iso2);
	if (!$result) {
		df_error(
			"Система не смогла узнать название страны с кодом «{$iso2}» для локали «%s»."
			,df_locale($locale)
		);
	}
	return $result;
}

/** 
 * 2016-05-20
 * @uses df_country_ctn()
 * @param string $iso2
 * @return string
 */
function df_country_ctn_ru($iso2) {return df_country_ctn($iso2, 'ru_RU');}

/**      
 * 2016-05-20
 * Возвращает 2-буквенный код страны по стандарту ISO 3166-1 alpha-2
 * по названию страны для заданной локали (или системной локали по умолчанию)
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 * @param string $name
 * @param string|null $locale [optional]
 * @return string|null
 */
function df_country_ntc($name, $locale = null) {
	df_param_sne($name, 0);
	return dfa(df_countries_ntc($locale), mb_strtoupper(df_trim($name)));
}

/**        
 * 2016-05-20
 * @uses df_country_ntc()
 * @param string $name
 * @return string|null
 */
function df_country_ntc_ru($name) {return df_country_ntc($name, 'ru_RU');}