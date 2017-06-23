<?php
use Magento\Framework\File\Csv;
/**
 * 2015-02-07
 * Эта функция аналогична функции @see df_csv_pretty(),
 * но предназначена для тех обработчиков данных, которые не допускают пробелов между элементами.
 * Если обработчик данных допускает пробелы между элементами,
 * то для удобочитаемости данных используйте функцию @see df_csv_pretty().
 * @param string[] ...$args
 * @return string
 */
function df_csv(...$args) {return implode(',', df_args($args));}

/**
 * 2017-06-23
 * It makes the single-word dictionary values quoted:
 * https://stackoverflow.com/questions/2489553
 * Usually it should be called after:
 * @see \Magento\Framework\File\Csv::saveData()
 * @see \Magento\Framework\Filesystem\Driver\File::filePutCsv()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @used-by \Dfr\Core\Console\Quote::execute()
 * @param string $f
 */
function df_csv_force_quotes($f) {file_put_contents($f, df_cc_n(df_clean(array_map(function($l) {
	/** @var string $c *//** @var string $d */
	$c = ','; $d = '"';
	$l = df_trim($l);
	// 2017-06-23
	// A workaround for the entries like «When using Store Code in URLs»
	// in https://raw.githubusercontent.com/magento/magento2/2.1.7/app/code/Magento/Backend/i18n/en_US.csv
	if ($l === $d || $l === "$d$c$d") {
		$l = null;
	}
	if ($l) {
		if (!df_starts_with($l, $d)) {
			$p = mb_strpos($l, $c);
			$l = $d . mb_substr($l, 0, $p) . $d . mb_substr($l, $p);
		}
		if (!df_ends_with($l, $d)) {
			$p = mb_strrpos($l, $c);
			$l = mb_substr($l, 0, $p + 1) . $d . mb_substr($l, $p + 1) . $d;
		}
	}
	return $l;
}, df_explode_n(file_get_contents($f))))));}

/**
 * 2017-06-21
 * @used-by df_intl_dic_read()
 * @return Csv
 */
function df_csv_o() {return df_om()->create(Csv::class);}

/**
 * 2015-02-07
 * @param string|null $s
 * @param string $delimiter [optional]
 * @return string[]
 */
function df_csv_parse($s, $delimiter = ',') {return !$s ? [] : df_trim(explode($delimiter, $s));}

/**
 * @param string|null $s
 * @return int[]
 */
function df_csv_parse_int($s) {return df_int(df_csv_parse($s));}

/**
 * 2015-02-07
 * Помимо данной функции имеется ещё аналогичная функция @see df_csv(),
 * которая предназначена для тех обработчиков данных, которые не допускают пробелов между элементами.
 * Если обработчик данных допускает пробелы между элементами,
 * то для удобочитаемости данных используйте функцию @see df_csv_pretty().
 * @used-by dfe_modules_log()
 * @used-by \Dfe\Moip\P\Reg::ga()
 * @param string[] ...$args
 * @return string
 */
function df_csv_pretty(...$args) {return implode(', ', dfa_flatten($args));}

/**
 * @param string[] ...$args
 * @return string
 */
function df_csv_pretty_quote(...$args) {return df_csv_pretty(df_quote_russian(df_args($args)));}

