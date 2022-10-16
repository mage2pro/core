<?php
/**
 * 2016-01-29
 * @see df_sort()
 * @used-by df_ksort_r()
 * @used-by df_stores()
 * @used-by df_trd_set()
 * @used-by \Df\Qa\Dumper::dumpArrayElements()
 * @used-by \Dfe\Dynamics365\API\Facade::p()
 * @used-by \Dfe\Qiwi\Signer::sign()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param array(int|string => mixed) $a
 * @param callable|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_ksort(array $a, $f = null) {
	// 2020-08-25
	// «`exception.values.0.stacktrace.frames`: Discarded invalid value» / «Reason: expected an array» in Sentry:
	// https://github.com/mage2pro/core/issues/139
	if (df_is_assoc($a)) {
		$f ? uksort($a, $f) : ksort($a);
	}
	return $a;
}

/**
 * 2017-08-22
 * Note 1. For now it is never used.
 * Note 2. An alternative implementation: df_ksort($a, 'strcasecmp')
 * 2017-09-07 Be careful! If the $a array is not associative,
 * then ksort($a, SORT_FLAG_CASE|SORT_STRING) will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * 2022-10-16 @deprecated It is unused.
 * @see df_ksort_r_ci()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort_ci(array $a) {ksort($a, SORT_FLAG_CASE|SORT_STRING); return $a;}

/**
 * 2017-07-05
 * @used-by df_ksort_r()
 * @used-by df_ksort_r_ci()
 * @param array(int|string => mixed) $a
 * @param callable|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_ksort_r(array $a, $f = null) {return df_ksort(df_map_k(function($k, $v) use($f) {return
	!is_array($v) ? $v : df_ksort_r($v, $f)
;}, $a), $f);}

/**
 * 2017-08-22
 * 2017-09-07 Be careful! If the $a array is not associative,
 * then df_ksort_r($a, 'strcasecmp') will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @used-by df_json_sort()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort_r_ci(array $a) {return !df_is_assoc($a)
	/**
	 * 2017-09-08
	 * @todo It would be nice to use df_sort($a) here,
	 * but now it will break the «Sales Documents Numeration» extension,
	 * because @see \Df\Config\Settings::_matrix() relies on an exact items ordering, e.g:
	 * [["ORD-{Y/m}-",null],["INV-",null],["SHIP-{Y-M}",null],["RET-{STORE-ID}-",null]]
	 * If we reorder these values, the «Sales Documents Numeration» extension will work incorrectly.
	 * I need to think how to improve it.
	 */
	? $a
	: df_ksort_r($a, 'strcasecmp')
;}

/**
 * 2016-07-18
 * 2016-08-10
 * С сегодняшнего дня я использую функцию @see df_caller_f(),
 * которая, в свою очередь, использует @debug_backtrace()
 * Это приводит к сбою: «Warning: usort(): Array was modified by the user comparison function».
 * http://stackoverflow.com/questions/3235387
 * https://bugs.php.net/bug.php?id=50688
 * По этой причине добавил собаку.
 * @see df_ksort()
 * @used-by df_json_sort()
 * @used-by df_sort_names()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Framework\Plugin\Css\PreProcessor\File\FileList\Collator::afterCollate()
 * @used-by \Df\Payment\Info\Report::sort()
 * @used-by \Df\Payment\TM::tResponses()
 * @used-by \Dfe\Color\Image::probabilities()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 * @param array(int|string => mixed) $a
 * @param \Closure|string|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_sort(array $a, $f = null) {
	$isAssoc = df_is_assoc($a); /** @var bool $isAssoc */
	if (!$f) {
		$isAssoc ? asort($a) : sort($a);
	}
	else {
		if (!$f instanceof \Closure) {
			$f = function($a, $b) use($f) {return !is_object($a) ? $a - $b : $a->$f() - $b->$f();};
		}
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$isAssoc ? @uasort($a, $f) : @usort($a, $f);
	}
	return $a;
}

/**
 * 2018-05-21
 * @used-by \Df\Config\Source\Block::map()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_sort_a(array $a) {asort($a); return $a;}

/**
 * 2017-02-02 http://stackoverflow.com/a/7930575
 * @used-by df_countries_options()
 * @used-by df_modules_p()
 * @used-by df_oqi_leafs()
 * @used-by df_zf_http_last_req()
 * @used-by dfe_portal_stripe_customers()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromCodeToName()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @param string[]|mixed[] $a
 * @param string|null $locale
 * @param callable|null $get
 * @return string[]|mixed[]
 */
function df_sort_names(array $a, $locale = null, callable $get = null) {
	$c = new \Collator($locale); /** @var \Collator $c */
	return df_sort($a, function($a, $b) use($c, $get) {return $c->compare(!$get ? $a : $get($a), !$get ? $b : $get($b));});
}