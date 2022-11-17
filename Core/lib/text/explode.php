<?php
/**
 * 2016-03-25 «charge.dispute.funds_reinstated» => [charge, dispute, funds, reinstated]
 * @param string[] $delimiters
 * @param string $s
 * @return string[]
 */
function df_explode_multiple(array $delimiters, $s):array {
	$main = array_shift($delimiters); /** @var string $main */
	# 2016-03-25
	# «If search is an array and replace is a string, then this replacement string is used for every value of search.»
	# https://php.net/manual/function.str-replace.php
	return explode($main, str_replace($delimiters, $main, $s));
}

/**
 * 2018-04-24 I have added @uses trim() today.
 * @used-by df_module_enum()
 * @used-by df_parse_colon()
 * @used-by df_tab_multiline()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\Core\Text\Regex::getSubjectSplitted()
 * @used-by \Dfe\AllPay\Charge::descriptionOnKiosk()
 * @used-by \Dfe\Moip\P\Charge::pInstructionLines()
 * @used-by \Dfe\TBCBank\W\Reader::reqFilter()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by \Inkifi\Core\Plugin\Catalog\Block\Product\View::afterSetLayout()
 * @param string $s
 * @return string[]
 */
function df_explode_n($s):array {return explode("\n", df_normalize(df_trim($s)));}

/**
 * 2016-09-03 Another implementation: df_explode_multiple(['/', DS], $path)
 * @used-by df_store_code_from_url()
 * @used-by df_url_trim_index()
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::pathA()
 * @param string $p
 * @return string[]
 */
function df_explode_path($p):array {return df_explode_xpath(df_path_n($p));}

/**
 * 2022-11-17
 * @used-by df_file_name()
 * @used-by df_magento_version_remote()
 * @used-by df_phone_explode()
 * @used-by df_webserver()
 * @used-by \Dfe\AmazonLogin\Customer::nameA()
 * @used-by \Df\Framework\Form\Element::getClassDfOnly()
 * @return string[]
 */
function df_explode_space(string $s):array {return df_trim(explode(' ', $s));}

/**
 * @used-by \TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 * @param string $url
 * @return string[]
 */
function df_explode_url($url):array {return explode('/', $url);}

/**
 * 2015-02-06
 * Если разделитель отсутствует в строке, то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
 * Это вполне укладывается в наш универсальный алгоритм.
 * @used-by df_explode_path()
 * @used-by dfa_deep()
 * @used-by dfa_deep_set()
 * @used-by dfa_deep_unset()
 * @used-by \Df\Config\Backend::value()
 * @param string|string[] $p
 * @return string[]
 */
function df_explode_xpath($p):array {return dfa_flatten(array_map(function($s) {return explode('/', $s);}, df_array($p)));}