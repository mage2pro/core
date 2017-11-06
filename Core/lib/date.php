<?php
use DateTime as DT;
use DateTimeZone as DTZ;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\Store;
use Zend_Date as ZD;
/**
 * 2016-07-19
 * @used-by df_num_days()
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @param Zend_Date|null $date [optional]
 * @return Zend_Date
 */
function df_date(ZD $date = null) {return $date ?: ZD::now();}

/**
 * @param int[] $args
 * @return ZD
 */
function df_date_create(...$args) {
	/** @var int $numberOfArguments */
	$numberOfArguments = count($args);
	/** @var string[] $paramKeys */
	$paramKeys = ['year', 'month', 'day', 'hour', 'minute', 'second'];
	/** @var int $countOfParamKeys */
	$countOfParamKeys = count($paramKeys);
	df_assert_between($numberOfArguments, 1, $countOfParamKeys);
	if ($countOfParamKeys > $numberOfArguments) {
		$args = array_merge($args, array_fill(0, $countOfParamKeys - $numberOfArguments, 0));
	}
	return new ZD(array_combine($paramKeys, $args));
}

/**
 * @param string $datetime
 * @param bool $throw [optional]
 * @return ZD|null
 * @throws Exception
 */
function df_date_from_db($datetime, $throw = true) {
	df_param_sne($datetime, 0);
	$result = null; /** @var ZD|null $result */
	if ($datetime) {
		try {
			$result = new ZD($datetime, ZD::ISO_8601);
		}
		catch (Exception $e) {
			if ($throw) {
				df_error($e);
			}
		}
	}
	return $result;
}

/**
 * Создаёт объект-дату по строке вида «20131115153657».
 * @param string $timestamp
 * @param string|null $offsetType [optional]
 * @return ZD
 */
function df_date_from_timestamp_14($timestamp, $offsetType = null) {
	df_assert(ctype_digit($timestamp));
	df_assert_eq(14, strlen($timestamp));
	// Почему-то new Zend_Date($timestamp, 'yMMddHHmmss') у меня не работает
	/** @var string $pattern */
	$pattern = '#(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})#';
	/** @var int[] $matches */
	$matches = [];
	df_assert_eq(1, preg_match($pattern, $timestamp, $matches));
	/** @var int $hour */
	$hour = df_nat0(dfa($matches, 4));
	if ($offsetType) {
		/** @var int $offsetFromGMT */
		$offsetFromGMT = df_round(df_int(df_dts(ZD::now(), ZD::TIMEZONE_SECS)) / 3600);
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
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return bool
 */
function df_date_gt(ZD $date1, ZD $date2) {return $date1->getTimestamp() > $date2->getTimestamp();}

/**
 * 2016-10-15
 * @return ZD
 */
function df_date_least() {return new ZD(0);}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return bool
 */
function df_date_lt(ZD $date1, ZD $date2) {return $date1->getTimestamp() < $date2->getTimestamp();}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return ZD
 */
function df_date_max(ZD $date1, ZD $date2) {return df_date_gt($date1, $date2) ? $date1 : $date2;}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return ZD
 */
function df_date_min(ZD $date1, ZD $date2) {return df_date_lt($date1, $date2) ? $date1 : $date2;}

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
	/** @var ZD|null $result */
	try {
		if ($tz) {
			date_default_timezone_set($tz);
		}
		$result = new ZD($dateS, $format);
		if ($tz) {
			// 2016-07-28
			// Эта операция конвертирует время из пояса $tz в пояс $defaultTZ.
			// Пример:
			// $dateS = «2016/07/28 11:35:03»,
			// $timezone = «Asia/Taipei»
			// $defaultTZ = «Europe/Moscow»
			// $result->toString() = 'Jul 28, 2016 6:35:03 AM'
			$result->setTimezone($defaultTZ);
		}
	}
	catch (\Exception $e) {
		if ($throw) {
			throw $e;
		}
		$result = null;
	}
	finally {
		if ($tz) {
			date_default_timezone_set($defaultTZ);
		}
	}
	return $result;
}

/**
 * 2016-07-19
 * @param ZD|null $date [optional]
 * @return ZD
 */
function df_date_reset_time(ZD $date = null) {
	/** @var ZD $result */
	$result = $date ? new ZD($date) : ZD::now();
	return $result->setHour(0)->setMinute(0)->setSecond(0);
}

/**
 * @param ZD $date
 * @param bool $inCurrentTimeZone [optional]
 * @return string
 */
function df_date_to_db(ZD $date, $inCurrentTimeZone = true) {
	return $date->toString($inCurrentTimeZone ? 'Y-MM-dd HH:mm:ss' : ZD::ISO_8601);
}

/**
 * @param int $days
 * @return string
 */
function df_day_noun($days) {
	/** @var string[] $forms */
	static $forms = ['день', 'дня', 'дней'];
	return df_t()->getNounForm($days, $forms);
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD|null $date [optional]
 * @return int
 */
function df_day_of_week_as_digit(ZD $date = null) {
	return df_nat0(df_date($date)->toString(Zend_Date::WEEKDAY_8601, 'iso'));
}

/**
 * @param int|null $min
 * @param int|null $max
 * @return string
 */
function df_days_interval($min, $max) {
	$min = df_nat0($min);
	$max = df_nat0($max);
	/** @var string $result */
	if (!$min && !$max) {
		$result = '';
	}
	else {
		/** @var string $dayNoun */
		if (!$max || $min === $max) {
			$dayNoun = df_day_noun($min);
			$result = "{$min} {$dayNoun}";
		}
		else {
			$dayNoun = df_day_noun($max);
			$result = "{$min}-{$max} {$dayNoun}";
		}
	}
	return $result;
}

/**
 * 2016-07-19
 * @param ZD|int|null $date
 * @return int|null
 */
function df_days_left($date) {return
	is_null($date) || is_int($date)
	? $date
	: df_num_days($date, ZD::now()) * (df_is_date_expired($date) ? -1 : 1)
;}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param null|string|int|ScopeA|Store $scope [optional]
 * @return int[]
 */
function df_days_off($scope = null) {return dfcf(function($scope = null) {return
	df_csv_parse_int(str_replace('0', '7', df_cfg('general/locale/weekend', $scope)))
;}, func_get_args());}

/**
 * 2015-02-07
 * Обратите внимание, что в сигнатуре метода/функции
 * для параметров объектного типа со значением по умолчанию null
 * мы вправе, тем не менее, указывать тип-класс.
 * Проверял на всех поддерживаемых Российской сборкой Magento версиях интерпретатора PHP,
 * сбоев нет:
 * http://3v4l.org/ihOFp
 *
 * Несмотря на свою спецификацию, @uses ZD::toString()
 * может вернуть не только строку, но и FALSE.
 * http://www.php.net/manual/en/function.date.php
 * http://php.net/gmdate
 *
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @param ZD|null $date [optional]
 * @param string|null $format [optional]
 * @param Zend_Locale|string|null $locale [optional]
 * @return string
 */
function df_dts(ZD $date = null, $format = null, $locale = null) {return df_result_sne(
	df_date($date)->toString($format, $type = null, $locale)
);}

/**
 * Переводит дату из одного строкового формата в другой
 * @param string $dateInSourceFormat
 * @param string $sourceFormat
 * @param string $resultFormat
 * @param bool $canBeEmpty [optional]
 * @return string
 */
function df_dtss($dateInSourceFormat, $sourceFormat, $resultFormat, $canBeEmpty = false) {
	/** @var string $result */
	$result = '';
	if (!$dateInSourceFormat) {
		df_assert($canBeEmpty, 'Пустая дата недопустима.');
	}
	else {
		$result = df_dts(new ZD($dateInSourceFormat, $sourceFormat), $resultFormat);
	}
	return $result;
}

/**
 * @param ZD|null $date [optional]
 * @return int
 */
function df_hour(ZD $date = null) {return df_nat0(df_date($date)->toString(ZD::HOUR_SHORT, 'iso'));}

/**
 * 2016-07-19
 * @param ZD $date
 * @return bool
 */
function df_is_date_expired(ZD $date) {
	return df_date_lt(df_date_reset_time($date), df_date_reset_time());
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date
 * @param null|string|int|ScopeA|Store $scope [optional]
 * @return bool
 */
function df_is_day_off(ZD $date, $scope = null) {
	return in_array(df_day_of_week_as_digit($date), df_days_off($scope));
}

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
 * @param Zend_Date $startDate
 * @param int $numWorkingDays
 * @param null|string|int|ScopeA|Store $store [optional]
 * @return int
 */
function df_num_calendar_days_by_num_working_days(ZD $startDate, $numWorkingDays, $store = null) {
	/** @var int $result */
	$result = $numWorkingDays;
	if ((0 === $result) && df_is_day_off($startDate)) {
		$result++;
	}
	/** @var int[] $daysOff */
	$daysOff = df_days_off($store);
	// все дни недели не могут быть выходными, иначе программа зависнет в цикле ниже
	df_assert_lt(7, count($daysOff));
	/** @var int $currentDayOfWeek */
	$currentDayOfWeek = df_day_of_week_as_digit($startDate);
	while (0 < $numWorkingDays) {
		if (in_array($currentDayOfWeek, $daysOff)) {
			$result++;
		}
		else {
			$numWorkingDays--;
		}
		$currentDayOfWeek = 1 + ($currentDayOfWeek % 7);
	}
	return $result;
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @param ZD|null $date1 [optional]
 * @param ZD|null $date2 [optional]
 * @return int
 */
function df_num_days(ZD $date1 = null, ZD $date2 = null) {
	$date1 = df_date($date1);
	$date2 = df_date($date2);
	/** @var ZD $dateMin */
	$dateMin = df_date_min($date1, $date2);
	/** @var ZD $dateMax */
	$dateMax = df_date_max($date1, $date2);
	/** http://stackoverflow.com/a/3118478 */
	/** @var Zend_Date $dateMinA */
	$dateMinA = df_date_reset_time($dateMin);
	/** @var Zend_Date $dateMaxA */
	$dateMaxA = df_date_reset_time($dateMax);
	/**
	 * Zend_Date::sub() возвращает число в виде строки для Magento CE 1.4.0.1
	 * и объект класса Zend_Date для более современных версий Magento
	 */
	$dateMaxA->sub($dateMinA);
	return df_round($dateMaxA->toValue() / 86400);
}

/**
 * 2015-04-07
 * @used-by df_tomorrow()
 * @used-by \Dfe\Moip\P\Charge::p()
 * @used-by \Dfe\Moip\T\CaseT\Payment\Boleto::pPayment()
 * @used-by \Dfe\Moip\T\CaseT\Payment\OnlineBanking::pPayment()
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
 * 2016-10-15
 * @return ZD
 */
function df_yesterday() {return df_today_sub(1);}