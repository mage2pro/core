<?php
use DateTime as DT;
use DateTimeZone as DTZ;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\Store;
use Zend_Date as ZD;
/**
 * 2016-07-19
 * @param Zend_Date|null $date [optional]
 * @return Zend_Date
 */
function df_date(ZD $date = null) {return $date ?: ZD::now();}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return bool
 */
function df_date_gt(ZD $date1, ZD $date2) {
	return $date1->getTimestamp() > $date2->getTimestamp();
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return bool
 */
function df_date_lt(ZD $date1, ZD $date2) {
	return $date1->getTimestamp() < $date2->getTimestamp();
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return ZD
 */
function df_date_max(ZD $date1, ZD $date2) {
	return df_date_gt($date1, $date2) ? $date1 : $date2;
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param ZD $date1
 * @param ZD $date2
 * @return ZD
 */
function df_date_min(ZD $date1, ZD $date2) {
	return df_date_lt($date1, $date2) ? $date1 : $date2;
}

/**
 * 2016-07-20
 * @param string $dateS
 * @param string $format
 * @param string|null $timezone [optional]
 * @return ZD
 */
function df_date_parse($dateS, $format, $timezone = null) {
	/** @var string $defaultTZ */
	if ($timezone) {
		$defaultTZ = date_default_timezone_get();
	}
	try {
		if ($timezone) {
			date_default_timezone_set($timezone);
		}
		/** @var ZD $result */
		$result = new ZD($dateS, $format);
		if ($timezone) {
			/**
			 * 2016-07-28
			 * Эта операция ковертирует время из пояса $timezone в пояс $defaultTZ.
			 * Пример:
			 * $dateS = «2016/07/28 11:35:03»,
			 * $timezone = «Asia/Taipei»
			 * $defaultTZ = «Europe/Moscow»
			 * $result->toString() = 'Jul 28, 2016 6:35:03 AM'
			 */
			$result->setTimezone($defaultTZ);
		}
	}
	finally {
		if ($timezone) {
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
 * @param ZD $date
 * @return int
 */
function df_days_left(ZD $date) {
	/** @var int $result */
	$result = df_num_days($date, ZD::now());
	return df_is_date_expired($date) ? -$result : $result;
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
 * @param null|string|int|ScopeA|Store $scope [optional]
 * @return int[]
 */
function df_days_off($scope = null) {
	/** @var array(int => int[]) $cache */
	static $cache;
	/** @var int $key */
	$key = df_store_id($scope);
	if (!isset($cache[$key])) {
		$cache[$key] = df_csv_parse_int(str_replace('0', '7', df_cfg('general/locale/weekend', $scope)));
	}
	return $cache[$key];
}

/**
 * 2015-02-07
 * Обратите внимание, что в сигнатуре метода/функции
 * для параметров объектного типа со значением по умолчанию null
 * мы вправе, тем не менее, указывать тип-класс.
 * Проверял на всех поддерживаемых Российской сборкой Magento версиях интерпретатора PHP,
 * сбоев нет:
 * http://3v4l.org/ihOFp
 * @param ZD|null $date [optional]
 * @param string|null $format [optional]
 * @param Zend_Locale|string|null $locale [optional]
 * @return string
 */
function df_dts(ZD $date = null, $format = null, $locale = null) {
	/** @var string|bool $result */
	$result = df_date($date)->toString($format, $type = null, $locale);
	/**
	 * Несмотря на свою спецификацию, @uses ZD::toString()
	 * может вернуть не только строку, но и FALSE.
	 * http://www.php.net/manual/en/function.date.php
	 * http://php.net/gmdate
	 */
	df_result_string_not_empty($result);
	return $result;
}

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
 * 2016-07-09          
 * http://stackoverflow.com/a/28447380
 * @param string $format
 * @param string|null $timezone [optional]   
 * @return string
 */
function df_now($format, $timezone = null) {
	return (new DT(null, !$timezone ? null : new DTZ($timezone)))->format($format);
}

/**
 * 2016-07-19
 * Портировал из Российской сборки Magento.
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
 * @param ZD $date1
 * @param ZD $date2
 * @return int
 */
function df_num_days(ZD $date1, ZD $date2) {
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
 * @param int $add
 * @return ZD
 */
function df_today_add($add) {return ZD::now()->addDay($add);}