<?php
/**
 * 2015-08-14
 * Мы не вправе кэшировать результат работы функции: ведь текущий магазин может меняться.
 * @return bool
 */
function df_is_admin() {
	/**
	 * 2015-09-20
	 * В отличие от Magento 1.x мы не можем использовать код
	 * Magento\Store\Model\Store::ADMIN_CODE === rm_store($store)->getCode();
	 * потому что при нахождении в административной части
	 * он вернёт вовсе не административную витрину, а витрину, указанную в MAGE_RUN_CODE.
	 * Более того, @see rm_store() учитывает параметры URL
	 * и даже при нахождении в административном интерфейсе
	 * может вернуть вовсе не административную витрину.
	 * Поэтому определяем нахождение в административном интерфейсе другим способом.
	 */
	return 'adminhtml' === rm_app_state()->getAreaCode();
}

/** @return bool */
function df_is_it_my_local_pc() {
	/** @var bool $result  */
	static $result;
	if (is_null($result)) {
		$result = rm_bool(df_a($_SERVER, 'RM_DEVELOPER'));
	}
	return $result;
}

/**
 * 2015-09-02
 * @return string
 */
function rm_action_name() {return rm_request_o()->getFullActionName();}

/**
 * 2015-09-20
 * @used-by df_is_admin()
 * @return \Magento\Framework\App\State
 */
function rm_app_state() {return df_o('Magento\Framework\App\State');}

/**
 * @return \Magento\Framework\App\Action\Action|null
 */
function rm_controller() {return rm_state()->controller();}

/**
 * https://mage2.ru/t/94
 * https://mage2.pro/t/59
 * @return bool
 */
function rm_is_ajax() {static $r; return !is_null($r) ? $r : $r = rm_request_o()->isXmlHttpRequest();}

/**
 * @param string $key
 * @param string|null $default [optional]
 * @return string
 */
function rm_request($key, $default = null) {return rm_request_o()->getParam($key, $default);}

/**
 * 2015-08-14
 * https://github.com/magento/magento2/issues/1675
 * @return \Magento\Framework\App\RequestInterface|\Magento\Framework\App\Request\Http
 */
function rm_request_o() {return df_o('Magento\Framework\App\RequestInterface');}

/**
 * 2015-08-14
 * @return string
 */
function rm_ruri() {static $r; return $r ? $r : $r = rm_request_o()->getRequestUri();}

/**
 * 2015-08-14
 * @param string $needle
 * @return bool
 */
function rm_ruri_contains($needle) {return rm_contains(rm_ruri(), $needle);}

/**
 * @return \Df\Core\State
 */
function rm_state() {static $r; return $r ? $r : $r = \Df\Core\State::s();}
