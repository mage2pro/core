<?php
# 2017-01-01
# Этот класс вычленяет из запроса параметры с приставкой «df-»
namespace Df\Framework;
class Request {
	/**
	 * 2017-01-01 Возвращает параметры запроса без приставки «df-».
	 * @used-by \Df\Payment\W\Reader::http()
	 * @param string|null $k [optional]
	 * @param mixed|null|callable $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	static function clean($k = null, $d = null) {return dfac(function() {return dfa_unset(
		df_request(), self::extraKeysRaw()
	);}, $k, $d);}

	/**
	 * 2017-01-01 Возвращает параметры запроса с приставкой «df-», при этом удалив эту приставку.
	 * @used-by \Df\Payment\W\Reader::__construct()
	 * @param string|null $k [optional]
	 * @param mixed|null|callable $d $key [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	static function extra($k = null, $d = null) {return dfac(function() {return dfak_transform(
		function($k) {return df_trim_text_left($k, 'df-');}, dfa(df_request(), self::extraKeysRaw())
	);}, $k, $d);}

	/**
	 * 2017-01-01
	 * @used-by self::clean()
	 * @used-by self::extra()
	 * @return array(string => mixed)
	 */
	private static function extraKeysRaw() {return dfcf(function() {return array_filter(
		array_keys(df_request()), function($k) {return df_starts_with($k, 'df-');}
	);});}
}