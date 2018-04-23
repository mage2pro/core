<?php
use Df\Core\R\ConT;
use Df\Shipping\Method as M;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface as IM;
/**
 * 2018-04-21
 * @used-by dfss()
 * @used-by \Df\Shipping\ConfigProvider::m()
 * @param mixed[] ...$args
 * @return M|IM
 */
function dfsm(...$args) {return dfcf(function(...$args) {
	/** @var array(string => M|IM) $cache */
	/** @var IM|object|string|null $src */
	if ($args) {
		$src = array_shift($args);
	}
	else {
		df_error('You can not use dfsm() without arguments yet.');
	}
	/** @var IM|M $result */
	if ($src instanceof IM) {
		$result = $src;
	}
	else {
		$result = M::sg($src);
		if ($args) {
			$result->setStore(df_store_id($args[0]));
		}
	}
	return $result;
}, func_get_args());}

/**
 * 2017-03-11
 * При текущей реализации мы осознанно не поддерживаем interceptors, потому что:
 * 1) Похоже, что невозможно определить, имеется ли для некоторого класса interceptor,
 * потому что вызов @uses class_exists(interceptor) приводит к созданию interceptor'а
 * (как минимум — в developer mode), даже если его раньше не было.
 * 2) У нас потомки Method объявлены как final.
 * @used-by dspm()
 * @used-by \Df\Shipping\Method::sg()
 * @param string|object $c
 * @param bool $allowAbstract [optional]
 * @return string
 */
function dfsm_c($c, $allowAbstract = false) {return dfcf(function($c, $allowAbstract = false) {return
	ConT::p($allowAbstract, function() use($c) {return df_con_heir($c, M::class);})
;}, func_get_args());}

/**
 * 2018-04-21
 * @used-by dfsm_code()
 * @param string|object $c
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function dfsm_call_s($c, $method, ...$params) {return df_con_s($c, 'Method', $method, $params);}

/**
 * 2018-04-21
 * @used-by dfsm_code_short()
 * @used-by \Df\Shipping\Settings::enable()
 * @uses \Df\Shipping\Method::codeS()
 * @param string|object $c
 * @return string
 */
function dfsm_code($c) {return dfcf(function($c) {return dfsm_call_s($c, 'codeS');}, [df_cts($c)]);}

/**
 * 2018-04-21
 * Без префикса «dfe_»
 * @used-by \Df\Shipping\Settings::prefix()
 * @uses \Df\Shipping\Method::codeS()
 * @param string|object $c
 * @return string
 */
function dfsm_code_short($c) {return df_trim_text_left(dfsm_code($c), 'dfe_');}