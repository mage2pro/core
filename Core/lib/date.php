<?php
/**
 * 2015-02-07
 * Обратите внимание, что в сигнатуре метода/функции
 * для параметров объектного типа со значением по умолчанию null
 * мы вправе, тем не менее, указывать тип-класс.
 * Проверял на всех поддерживаемых Российской сборкой Magento версиях интерпретатора PHP,
 * сбоев нет:
 * http://3v4l.org/ihOFp
 * @param \Zend_Date|null $date [optional]
 * @param string|null $format [optional]
 * @param Zend_Locale|string|null $locale [optional]
 * @return string
 */
function df_dts(\Zend_Date $date = null, $format = null, $locale = null) {
	if (!$date) {
		$date = \Zend_Date::now();
	}
	/** @var string|bool $result */
	$result = $date->toString($format, $type = null, $locale);
	/**
	 * Несмотря на свою спецификацию, @uses \Zend_Date::toString()
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
		$result = df_dts(new \Zend_Date($dateInSourceFormat, $sourceFormat), $resultFormat);
	}
	return $result;
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
 * 2015-04-07
 * @param int $add
 * @return \Zend_Date
 */
function df_today_add($add) {return \Zend_Date::now()->addDay($add);}