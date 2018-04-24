<?php
use Magento\Framework\File\Csv;
/**
 * 2015-02-07
 * Эта функция аналогична функции @see df_csv_pretty(),
 * но предназначена для тех обработчиков данных, которые не допускают пробелов между элементами.
 * Если обработчик данных допускает пробелы между элементами,
 * то для удобочитаемости данных используйте функцию @see df_csv_pretty().
 * @used-by df_oro_get_list()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 * @used-by \Dfe\CheckoutCom\Method::disableEvent()
 * @used-by \Dfe\FacebookLogin\Customer::responseA()
 * @param string[] ...$args
 * @return string
 */
function df_csv(...$args) {return implode(',', df_args($args));}

/**
 * 2017-06-21
 * @used-by df_intl_dic_read()  
 * @used-by df_module_csv2()
 * @return Csv
 */
function df_csv_o() {return df_new_om(Csv::class);}

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

