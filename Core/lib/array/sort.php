<?php
/**
 * 2016-01-29
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @see df_sort()
 * @used-by df_ksort_r()
 * @used-by df_stores()
 * @used-by df_trd_set()
 * @used-by \Df\Qa\Dumper::dumpArrayElements()
 * @used-by \Dfe\Dynamics365\API\Facade::p()
 * @used-by \Dfe\Qiwi\Signer::sign()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort(array $a, callable $f = null):array {
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
function df_ksort_ci(array $a):array {ksort($a, SORT_FLAG_CASE|SORT_STRING); return $a;}

/**
 * 2017-07-05
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by df_ksort_r()
 * @used-by df_ksort_r_ci()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort_r(array $a, callable $f = null):array {return df_ksort(df_map_k(function($k, $v) use($f) {return
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
function df_ksort_r_ci(array $a):array {return array_is_list($a)
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
 * которая, в свою очередь, использует @debug_backtrace().
 * Это приводит к сбою: «Warning: usort(): Array was modified by the user comparison function».
 * http://stackoverflow.com/questions/3235387
 * https://bugs.php.net/bug.php?id=50688
 * По этой причине добавил собаку.
 * 2022-11-30
 * «Deprecated Functionality: Collator::__construct():
 * Passing null to parameter #1 ($locale) of type string is deprecated
 * in vendor/justuno.com/core/lib/Core/array/sort.php on line 102»:
 * https://github.com/justuno-com/core/issues/379
 * 2024-04-08
 * 1) From now on, it is locale-aware (previously, I had a separate locale-aware df_sort_l() / df_sort_names() function).
 * 2) https://3v4l.org/iV0BT
 * 3) $isGet = true in:
 * 		@used-by df_oqi_leafs()
 * 		@used-by dfe_portal_stripe_customers()
 * 		@used-by \Dfe\Oro\Test\Basic::t02_orders_stripe()
 * 4) $l is used in:
 * 		@used-by df_oqi_leafs()
 * 		@used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromCodeToName()
 * @see df_ksort()
 * @used-by df_countries_options()
 * @used-by df_json_sort()
 * @used-by df_modules_p()
 * @used-by df_oqi_leafs()
 * @used-by df_sort_l()
 * @used-by df_zf_http_last_req()
 * @used-by dfe_portal_stripe_customers()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromCodeToName()
 * @used-by \Df\Framework\Plugin\Css\PreProcessor\File\FileList\Collator::afterCollate()
 * @used-by \Df\Payment\Info\Report::sort()
 * @used-by \Df\Payment\TM::tResponses()
 * @used-by \Dfe\Color\Image::probabilities()
 * @used-by \Dfe\Oro\Test\Basic::t02_orders_stripe()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @used-by \CabinetsBay\Catalog\B\Category::images() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 * @param array(int|string => mixed) $a
 * @param Closure|string|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_sort(array $a, $f = null, bool $isGet = false, string $l = ''):array {
	# 2017-02-02 http://stackoverflow.com/a/7930575
	$c = new Collator($l); /** @var Collator $c */
	$isList = array_is_list($a); /** @var bool $isList */
	if (!$f) {
		/**
		 * 2024-06-10
		 * 1) "The gallery images on category pages should be ordered according to their file names":
		 * https://github.com/cabinetsbay/catalog/issues/28
		 * 2) The following code does not work correctly for an unknwon reason:
		 * 		$c = new Collator('');
		 * 		$a = ['3628/swuc_5_7.jpg', '3628/swuc_1_1.jpg', '3628/swuc_4.jpg', '3628/swuc_1.jpg'];
		 * 		$c->sort($a);
		 * https://3v4l.org/LlLrB
		 * It returns:
		 * 	Array (
		 * 		[0] => 3628/swuc_5_7.jpg
		 * 		[1] => 3628/swuc_1_1.jpg
		 * 		[2] => 3628/swuc_4.jpg
		 * 		[3] => 3628/swuc_1.jpg
		 * )
		 * 3) `echo intval(is_numeric('3628/swuc_5_7.jpg'));` returns `0`: https://3v4l.org/SDiX7
		 * 4) The code from 2 with @see Collator::SORT_STRING works almost correctly:
		 * 		$c = new Collator('');
		 * 		$a = ['3628/swuc_5_7.jpg', '3628/swuc_1_1.jpg', '3628/swuc_4.jpg', '3628/swuc_1.jpg'];
		 * 		$c->sort($a, Collator::SORT_STRING);
		 * 		echo locale_get_default() . "\n";
		 * 		print_r($a);
		 * 4.1) It works correctly on 3v4l.org:
		 * 	Array (
		 * 		[0] => 3628/swuc_1.jpg
		 * 		[1] => 3628/swuc_1_1.jpg
		 * 		[2] => 3628/swuc_4.jpg
		 * 		[3] => 3628/swuc_5_7.jpg
		 *	 )
		 * https://3v4l.org/RkBvh
		 * But it works just because 3v4l.org use the 'en_US_POSIX' locale:
		 * https://www.localeplanet.com/icu/en-US-POSIX/index.html
		 * https://superuser.com/questions/1519501
		 * 4.2) The first 2 elements of the test array are inverted with other locales, e.g.:
		 * 4.2.1) 'en_US': https://3v4l.org/FMcdf
		 * 4.2.2) 'root': https://3v4l.org/Cevuk
		 */
		$mode = is_string(df_first($a)) ? Collator::SORT_STRING : Collator::SORT_REGULAR;
		$isList ? $c->sort($a, $mode) : $c->asort($a, $mode);
	}
	else {
		if ($isGet) {
			$f = function($a, $b) use($c, $f):int {return $c->compare(!$f ? $a : $f($a), !$f ? $b : $f($b));};
		}
		elseif (!$f instanceof Closure) {
			$f = function($a, $b) use($f):int {return !is_object($a) ? $a - $b : $a->$f() - $b->$f();};
		}
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$isList ? @usort($a, $f) : @uasort($a, $f);
	}
	return $a;
}

/**
 * 2018-05-21
 * @used-by \Df\Config\Source\Block::map()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_sort_a(array $a):array {asort($a); return $a;}