<?php
use DateTime as DT;
use DateTimeZone as DTZ;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\Store;
use Zend_Date as ZD;
use \Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2016-07-19
 * @used-by df_day_of_week_as_digit()
 * @used-by df_dts()
 * @used-by df_hour()
 * @used-by df_month()
 * @used-by df_num_days()
 * @used-by df_year()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 */
function df_date(ZD $d = null):ZD {return $d ?: ZD::now();}

/** 2022-10-29 @deprecated It is unused. */
function df_date_create(int ...$a):ZD {
	$numberOfArguments = count($a); /** @var int $numberOfArguments */
	$paramKeys = ['year', 'month', 'day', 'hour', 'minute', 'second']; /** @var string[] $paramKeys */
	$countOfParamKeys = count($paramKeys); /** @var int $countOfParamKeys */
	df_assert_between($numberOfArguments, 1, $countOfParamKeys);
	if ($countOfParamKeys > $numberOfArguments) {
		$a = array_merge($a, array_fill(0, $countOfParamKeys - $numberOfArguments, 0));
	}
	return new ZD(array_combine($paramKeys, $a));
}

/**
 * @used-by \Df\Payment\Operation::customerDob()
 * @see df_date_to_db()
 * @return ZD|null
 * @throws Exception
 */
function df_date_from_db(string $s, bool $onE = true) {
	df_param_sne($s, 0); return df_try(function() use($s):ZD {return new ZD($s, ZD::ISO_8601);}, $onE);
}

/**
 * Создаёт объект-дату по строке вида «20131115153657».
 * 2022-10-29 @deprecated It is unused.
 * @param string|null $offsetType [optional]
 */
function df_date_from_timestamp_14(string $timestamp, $offsetType = null):ZD {
	df_assert(ctype_digit($timestamp));
	df_assert_eq(14, strlen($timestamp));
	# Почему-то new Zend_Date($timestamp, 'yMMddHHmmss') у меня не работает
	$pattern = '#(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})#'; /** @var string $pattern */
	$matches = []; /** @var int[] $matches */
	df_assert_eq(1, preg_match($pattern, $timestamp, $matches));
	$hour = df_nat0(dfa($matches, 4)); /** @var int $hour */
	if ($offsetType) {
		$offsetFromGMT = df_round(df_int(df_dts(ZD::now(), ZD::TIMEZONE_SECS)) / 3600); /** @var int $offsetFromGMT */
		$hour += $offsetFromGMT;
		if ('UTC' === df_assert_in($offsetType, ['UTC', 'GMT'])) {
			$hour++;
		}
	}
	return new ZD([
		'year' => dfa($matches, 1)
	   ,'month' => dfa($matches, 2)
	   ,'day' => dfa($matches, 3)
	   ,'hour' => $hour
	   ,'minute' => dfa($matches, 5)
	   ,'second' => dfa($matches, 6)
	]);
}

/**
 * 2016-07-19
 * @used-by df_date_max()
 */
function df_date_gt(ZD $d1, ZD $d2):bool {return $d1->getTimestamp() > $d2->getTimestamp();}

/**
 * 2016-10-15
 * 2022-10-29 @deprecated It is unused.
 */
function df_date_least():ZD {return new ZD(0);}

/**
 * 2016-07-19
 * @used-by df_date_min()
 * @used-by df_is_date_expired()
 */
function df_date_lt(ZD $d1, ZD $d2):bool {return $d1->getTimestamp() < $d2->getTimestamp();}

/**
 * 2016-07-19
 * @used-by df_num_days()
 * @param ZD $d1
 * @param ZD $d2
 */
function df_date_max(ZD $d1, ZD $d2):ZD {return df_date_gt($d1, $d2) ? $d1 : $d2;}

/**
 * 2016-07-19
 * @used-by df_num_days()
 */
function df_date_min(ZD $d1, ZD $d2):ZD {return df_date_lt($d1, $d2) ? $d1 : $d2;}

/**
 * 2016-07-20
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @used-by \Dfe\AllPay\W\Event::time()
 * @param string|null $fmt [optional]
 * @return ZD|null
 * @throws Th
 */
function df_date_parse(string $dateS, bool $throw = true, $fmt = null, string $tz = '') {
	/** @var string $defaultTZ */
	if ($tz) {
		$defaultTZ = date_default_timezone_get();
	}
	/** @var ZD|null $r */
	try {
		if ($tz) {
			date_default_timezone_set($tz);
		}
		$r = new ZD($dateS, $fmt);
		if ($tz) {
			# 2016-07-28
			# Эта операция конвертирует время из пояса $tz в пояс $defaultTZ.
			# Пример:
			# $dateS = «2016/07/28 11:35:03»,
			# $timezone = «Asia/Taipei»
			# $defaultTZ = «Europe/Moscow»
			# $r->toString() = 'Jul 28, 2016 6:35:03 AM'
			$r->setTimezone($defaultTZ);
		}
	}
	catch (Th $th) {
		if ($throw) {
			throw $th;
		}
		$r = null;
	}
	finally {
		if ($tz) {
			date_default_timezone_set($defaultTZ);
		}
	}
	return $r;
}

/**
 * 2016-07-19
 * @used-by df_is_date_expired()
 * @used-by df_num_days()
 * @used-by df_today_add()
 * @used-by df_today_sub()
 */
function df_date_reset_time(ZD $date = null):ZD {
	$r = $date ? new ZD($date) : ZD::now(); /** @var ZD $r */
	return $r->setHour(0)->setMinute(0)->setSecond(0);
}

/**
 * 2022-10-29 @deprecated It is unused.
 * @see df_date_from_db()
 */
function df_date_to_db(ZD $date, bool $inCurrentTimeZone = true):string {return $date->toString(
	$inCurrentTimeZone ? 'Y-MM-dd HH:mm:ss' : ZD::ISO_8601
);}

/**
 * 2016-07-19 Портировал из Российской сборки Magento.
 * @used-by df_is_day_off()
 * @used-by df_num_calendar_days_by_num_working_days()
 */
function df_day_of_week_as_digit(ZD $date = null):int {return df_nat0(df_date($date)->toString(ZD::WEEKDAY_8601, 'iso'));}

/**
 * 2016-07-19
 * @used-by \Dfe\AllPay\W\Event\Offline::expirationS()
 * @param ZD|int|null $d
 * @return int|null
 */
function df_days_left($d) {return
	is_null($d) || is_int($d) ? $d : df_num_days($d, ZD::now()) * (df_is_date_expired($d) ? -1 : 1)
;}

/**
 * 2016-07-19
 * @used-by df_is_day_off()
 * @used-by df_num_calendar_days_by_num_working_days()
 * @param null|string|int|ScopeA|Store $s [optional]
 * @return int[]
 */
function df_days_off($s = null):array {return dfcf(function($s = null) {return df_csv_parse_int(str_replace(
	'0', '7', df_cfg('general/locale/weekend', $s)
));}, func_get_args());}

/**
 * 2015-02-07
 * 1) Обратите внимание, что в сигнатуре метода/функции для параметров объектного типа со значением по умолчанию null
 * мы вправе, тем не менее, указывать тип-класс.
 * Проверял на всех поддерживаемых Российской сборкой Magento версиях интерпретатора PHP, сбоев нет:
 * http://3v4l.org/ihOFp
 * 2) Несмотря на свою спецификацию, @uses ZD::toString() может вернуть не только строку, но и FALSE.
 * http://www.php.net/manual/en/function.date.php
 * https://php.net/gmdate
 * @used-by df_date_from_timestamp_14()
 * @used-by df_dtss()
 * @used-by df_file_name()
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @used-by \Dfe\AllPay\W\Event\Offline::expirationS()
 * @used-by \TFC\GoogleShopping\Result::contents() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
 * @param string|null $fmt [optional]
 * @param Zend_Locale|string|null $l [optional]
 */
function df_dts(ZD $d = null, $fmt = null, $l = null):string {return df_result_sne(df_date($d)->toString($fmt, null, $l));}

/**
 * Переводит дату из одного строкового формата в другой.
 * 2022-10-29 @deprecated It is unused.
 */
function df_dtss(string $dateInSourceFormat, string $sourceFormat, string $resultFormat, bool $canBeEmpty = false):string {
	/** @var string $r */
	if ($dateInSourceFormat) {
		$r = df_dts(new ZD($dateInSourceFormat, $sourceFormat), $resultFormat);
	}
	else {
		df_assert($canBeEmpty, 'Empty $dateInSourceFormat is not allowed.');
		$r = '';
	}
	return $r;
}

/**
 * 2022-10-29 @deprecated It is unused.
 * @see df_month()
 * @see df_year()
 */
function df_hour(ZD $date = null):int {return df_nat0(df_date($date)->toString(ZD::HOUR_SHORT, 'iso'));}

/**
 * 2016-07-19
 * @used-by df_days_left()
 */
function df_is_date_expired(ZD $date):bool {return df_date_lt(df_date_reset_time($date), df_date_reset_time());}

/**
 * 2016-07-19 Портировал из Российской сборки Magento.
 * @used-by df_num_calendar_days_by_num_working_days()
 * @param null|string|int|ScopeA|Store $s [optional]
 */
function df_is_day_off(ZD $d, $s = null):bool {return in_array(df_day_of_week_as_digit($d), df_days_off($s));}

/**
 * 2018-11-13
 * @see df_hour()
 * @see df_year()
 * @used-by \Df\StripeClone\Facade\Card::isActive()
 * @used-by \Dfe\TBCBank\Test\CaseT\Regular::t04()
 */
function df_month(ZD $d = null):int {return df_nat0(df_date($d)->toString(ZD::MONTH, 'iso'));}

/**
 * 2016-07-09 http://stackoverflow.com/a/28447380
 * 2023-07-20
 * 1) «DateTime::__construct(): Passing null to parameter #1 ($datetime) of type string is deprecated»:
 * https://github.com/mage2pro/core/issues/241
 * 2) The 2nd parameter (timezone) can be `null`: https://3v4l.org/m76dH
 * @used-by \Dfe\AllPay\Charge::pCharge()
 * @used-by \Dfe\CheckoutCom\Charge::pMetadata()
 * @param string|null $tz [optional]
 */
function df_now(string $fmt, $tz = null):string {return (new DT('', !$tz ? null : new DTZ($tz)))->format($fmt);}

/**
 * 2016-07-19 Портировал из Российской сборки Magento.  
 * @used-by \Df\Config\Source\WaitPeriodType::calculate()
 * @param null|string|int|ScopeA|Store $store [optional]
 */
function df_num_calendar_days_by_num_working_days(ZD $startDate, int $numWorkingDays, $store = null):int {
	$r = $numWorkingDays; /** @var int $r */
	if ((0 === $r) && df_is_day_off($startDate)) {
		$r++;
	}
	$daysOff = df_days_off($store); /** @var int[] $daysOff */
	df_assert_lt(7, count($daysOff)); # все дни недели не могут быть выходными, иначе программа зависнет в цикле ниже
	$currentDayOfWeek = df_day_of_week_as_digit($startDate); /** @var int $currentDayOfWeek */
	while (0 < $numWorkingDays) {
		if (in_array($currentDayOfWeek, $daysOff)) {
			$r++;
		}
		else {
			$numWorkingDays--;
		}
		$currentDayOfWeek = 1 + ($currentDayOfWeek % 7);
	}
	return $r;
}

/**
 * 2016-07-19 Портировал из Российской сборки Magento.
 * @used-by df_days_left()
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 */
function df_num_days(ZD $d1 = null, ZD $d2 = null):int {
	$d1 = df_date($d1);
	$d2 = df_date($d2);
	$dateMin = df_date_min($d1, $d2); /** @var ZD $dateMin */
	$dateMax = df_date_max($d1, $d2); /** @var ZD $dateMax */
	/** http://stackoverflow.com/a/3118478 */
	$dateMinA = df_date_reset_time($dateMin); /** @var ZD $dateMinA */
	$dateMaxA = df_date_reset_time($dateMax); /** @var ZD $dateMaxA */
	/**
	 * @uses ZD::sub() возвращает число в виде строки для Magento CE 1.4.0.1
	 * и объект класса @see ZD для более современных версий Magento.
	 */
	$dateMaxA->sub($dateMinA);
	return df_round($dateMaxA->toValue() / 86400);
}

/**
 * 2015-04-07
 * @used-by df_tomorrow()
 * @used-by \Dfe\Moip\P\Charge::p()
 * @used-by \Dfe\Moip\Test\CaseT\Payment\Boleto::pPayment()
 * @used-by \Dfe\Moip\Test\CaseT\Payment\OnlineBanking::pPayment()
 */
function df_today_add(int $add):ZD {return df_date_reset_time(ZD::now()->addDay($add));}

/**
 * 2016-10-15
 * @used-by df_yesterday()
 */
function df_today_sub(int $sub):ZD {return df_date_reset_time(ZD::now()->subDay($sub));}

/**
 * 2016-10-15
 * 2022-10-29 @deprecated It is unused.
 */
function df_tomorrow():ZD {return df_today_add(1);}

/**
 * 2018-11-13
 * @see df_hour()
 * @see df_month()
 * @used-by \Df\StripeClone\Facade\Card::isActive()
 * @used-by \Dfe\TBCBank\Test\CaseT\Regular::t04()
 */
function df_year(ZD $date = null):int {return df_nat0(df_date($date)->toString(ZD::YEAR, 'iso'));}

/**
 * 2016-10-15
 * 2022-10-29 @deprecated It is unused.
 */
function df_yesterday():ZD {return df_today_sub(1);}