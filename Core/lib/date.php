<?php
use DateTime as DT;
use DateTimeZone as DTZ;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\Store;
use Zend_Date as ZD;
/**
 * 2016-07-19
 * @used-by df_day_of_week_as_digit()
 * @used-by df_dts()
 * @used-by df_hour()
 * @used-by df_month()
 * @used-by df_num_days()
 * @used-by df_year()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @param ZD|null $date [optional]
 */
function df_date(ZD $date = null):ZD {return $date ?: ZD::now();}

/**
 * 2022-10-29 @deprecated It is unused.
 * @param int|int[] ...$args
 */
function df_date_create(...$args):ZD {
	$numberOfArguments = count($args); /** @var int $numberOfArguments */
	$paramKeys = ['year', 'month', 'day', 'hour', 'minute', 'second']; /** @var string[] $paramKeys */
	$countOfParamKeys = count($paramKeys); /** @var int $countOfParamKeys */
	df_assert_between($numberOfArguments, 1, $countOfParamKeys);
	if ($countOfParamKeys > $numberOfArguments) {
		$args = array_merge($args, array_fill(0, $countOfParamKeys - $numberOfArguments, 0));
	}
	return new ZD(array_combine($paramKeys, $args));
}

/**
 * @used-by \Df\Payment\Operation::customerDob()
 * @see df_date_to_db()
 * @param string $datetime
 * @param bool $throw [optional]
 * @return ZD|null
 * @throws Exception
 */
function df_date_from_db($datetime, $throw = true) {
	df_param_sne($datetime, 0);
	$r = null; /** @var ZD|null $r */
	if ($datetime) {
		try {$r = new ZD($datetime, ZD::ISO_8601);}
		catch (Exception $e) {
			if ($throw) {
				df_error($e);
			}
		}
	}
	return $r;
}

/**
 * Создаёт объект-дату по строке вида «20131115153657».
 * 2022-10-29 @deprecated It is unused.
 * @param string $timestamp
 * @param string|null $offsetType [optional]
 * @return ZD
 */
function df_date_from_timestamp_14($timestamp, $offsetType = null) {
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
 * @param ZD $d1
 * @param ZD $d2
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
 * @param ZD $d1
 * @param ZD $d2
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
 * @param ZD $d1
 * @param ZD $d2
 */
function df_date_min(ZD $d1, ZD $d2):ZD {return df_date_lt($d1, $d2) ? $d1 : $d2;}

/**
 * 2016-07-20
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @used-by \Dfe\AllPay\W\Event::time()
 * @param string $dateS
 * @param bool $throw [optional]
 * @param string|null $format [optional]
 * @param string|null $tz [optional]
 * @return ZD|null
 * @throws \Exception
 */
function df_date_parse($dateS, $throw = true, $format = null, $tz = null) {
	/** @var string $defaultTZ */
	if ($tz) {
		$defaultTZ = date_default_timezone_get();
	}
	/** @var ZD|null $r */
	try {
		if ($tz) {
			date_default_timezone_set($tz);
		}
		$r = new ZD($dateS, $format);
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
	catch (\Exception $e) {
		if ($throw) {
			throw $e;
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
 * @param ZD|null $date [optional]
 */
function df_date_reset_time(ZD $date = null):ZD {
	$r = $date ? new ZD($date) : ZD::now(); /** @var ZD $r */
	return $r->setHour(0)->setMinute(0)->setSecond(0);
}

/**
 * 2022-10-29 @deprecated It is unused.
 * @see df_date_from_db()
 * @param ZD $date
 * @param bool $inCurrentTimeZone [optional]
 */
function df_date_to_db(ZD $date, $inCurrentTimeZone = true):string {return $date->toString(
	$inCurrentTimeZone ? 'Y-MM-dd HH:mm:ss' : ZD::ISO_8601
);}

/**
 * 2016-07-19 Портировал из Российской сборки Magento.
 * @used-by df_is_day_off()
 * @used-by df_num_calendar_days_by_num_working_days()
 * @param ZD|null $date [optional]
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
 * http://php.net/gmdate
 * @used-by df_date_from_timestamp_14()
 * @used-by df_dtss()
 * @used-by df_file_name()
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @used-by \Dfe\AllPay\W\Event\Offline::expirationS()
 * @used-by \TFC\GoogleShopping\Result::contents() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
 * @param ZD|null $date [optional]
 * @param string|null $format [optional]
 * @param Zend_Locale|string|null $locale [optional]
 */
function df_dts(ZD $date = null, $format = null, $locale = null):string {return df_result_sne(
	df_date($date)->toString($format, $type = null, $locale)
);}

/**
 * Переводит дату из одного строкового формата в другой.
 * 2022-10-29 @deprecated It is unused.
 * @param string $dateInSourceFormat
 * @param string $sourceFormat
 * @param string $resultFormat
 * @param bool $canBeEmpty [optional]
 */
function df_dtss($dateInSourceFormat, $sourceFormat, $resultFormat, $canBeEmpty = false):string {/** @var string $r */
	if (!$dateInSourceFormat) {
		df_assert($canBeEmpty, 'Пустая дата недопустима.');
		$r = '';
	}
	else {
		$r = df_dts(new ZD($dateInSourceFormat, $sourceFormat), $resultFormat);
	}
	return $r;
}

/**
 * 2022-10-29 @deprecated It is unused.
 * @see df_month()
 * @see df_year()
 * @param ZD|null $date [optional]
 */
function df_hour(ZD $date = null):int {return df_nat0(df_date($date)->toString(ZD::HOUR_SHORT, 'iso'));}

/**
 * 2016-07-19
 * @used-by df_days_left()
 * @param ZD $date
 */
function df_is_date_expired(ZD $date):bool {return df_date_lt(df_date_reset_time($date), df_date_reset_time());}

/**
 * 2016-07-19 Портировал из Российской сборки Magento.
 * @used-by df_num_calendar_days_by_num_working_days()
 * @param ZD $d
 * @param null|string|int|ScopeA|Store $s [optional]
 */
function df_is_day_off(ZD $d, $s = null):bool {return in_array(df_day_of_week_as_digit($d), df_days_off($s));}

/**
 * 2018-11-13
 * @see df_hour()
 * @see df_year()
 * @used-by \Df\StripeClone\Facade\Card::isActive()
 * @used-by \Dfe\TBCBank\Test\CaseT\Regular::t04()
 * @param ZD|null $date [optional]
 */
function df_month(ZD $date = null):int {return df_nat0(df_date($date)->toString(ZD::MONTH, 'iso'));}

/**
 * 2016-07-09 http://stackoverflow.com/a/28447380
 * @used-by \Dfe\AllPay\Charge::pCharge()
 * @used-by \Dfe\CheckoutCom\Charge::pMetadata()
 * @param string $format
 * @param string|null $timezone [optional]   
 * @return string
 */
function df_now($format, $timezone = null) {return
	(new DT(null, !$timezone ? null : new DTZ($timezone)))->format($format)
;}

/**
 * 2016-07-19 Портировал из Российской сборки Magento.  
 * @used-by \Df\Config\Source\WaitPeriodType::calculate()
 * @param ZD $startDate
 * @param int $numWorkingDays
 * @param null|string|int|ScopeA|Store $store [optional]
 * @return int
 */
function df_num_calendar_days_by_num_working_days(ZD $startDate, $numWorkingDays, $store = null) {
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
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @param ZD|null $d1 [optional]
 * @param ZD|null $d2 [optional]
 * @return int
 */
function df_num_days(ZD $d1 = null, ZD $d2 = null) {
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
 * @param int $add
 * @return ZD
 */
function df_today_add($add) {return df_date_reset_time(ZD::now()->addDay($add));}

/**
 * 2016-10-15
 * @param int $sub
 * @return ZD
 */
function df_today_sub($sub) {return df_date_reset_time(ZD::now()->subDay($sub));}

/**
 * 2016-10-15
 * @return ZD
 */
function df_tomorrow() {return df_today_add(1);}

/**
 * 2018-11-13
 * @see df_hour()
 * @used-by \Df\StripeClone\Facade\Card::isActive()
 * @param ZD|null $date [optional]
 * @return int
 */
function df_year(ZD $date = null) {return df_nat0(df_date($date)->toString(ZD::YEAR, 'iso'));}

/**
 * 2016-10-15
 * @return ZD
 */
function df_yesterday() {return df_today_sub(1);}