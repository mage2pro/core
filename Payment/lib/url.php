<?php
use Df\Payment\Method as M;
/**
 * 2017-01-22
 * @param string $url
 * @param bool|object $test
 * @param string[] $stageNames
 * @param mixed[] ...$params [optional]
 * @return string
 */
function dfp_url($url, $test, array $stageNames, ...$params) {
	if ($test instanceof M) {
		$test = $test->s()->test();
	}
	df_assert_boolean($test);
	/** @var string $stage */
	$stage = $test ? df_first($stageNames) : df_last($stageNames);
	return vsprintf(str_replace('{stage}', $stage, $url), df_args($params));
}