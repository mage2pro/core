<?php
use Df\Customer\Setup\UpgradeSchema as Schema;
use Magento\Customer\Model\Customer as C;
use Magento\Framework\DataObject;

/**
 * 2016-08-22
 * @see dfp_add_info()
 * 2016-08-23
 * Если значением ключа в $info будет null, то предыдущий ключ удалится: @see dfo()
 * @param DataObject|C $c
 * @param array(string => mixed) $info
 * @return void
 */
function df_customer_info_add(DataObject $c, array $info) {
	$c[Schema::F__DF] = df_json_encode(df_extend(df_customer_info_get($c), $info));
}

/**
 * 2016-08-22
 * @param DataObject|C|null $c [optional]
 * @param string $path [optional]
 * @param string|array(string => mixed)|null $default [optional]
 * @return string|array(string => mixed)|null
 */
function df_customer_info_get(DataObject $c = null, $path = null, $default = null) {
	$c = $c ?: df_current_customer();
	/** @var string|array(string => mixed)|null $result */
	if (!$c) {
		$result = null;
	}
	else {
		/** @var string $json */
		$json = $c[Schema::F__DF];
		/** @var array(string => mixed) $array */
		$array = is_null($json) ? [] : df_json_decode($json);
		$result = is_null($path) ? $array : dfa_deep($array, $path, $default);
	}
	return $result;
}

/**
 * 2016-08-24
 * @param string $module
 * @param mixed|null $info
 * @param C|null $c [optional]
 * @return void
 */
function df_customer_info_save($module, $info, C $c = null) {
	$c = $c ?: df_current_customer();
	/** @var array(string => string) $data */
	$data = [$module => $info];
	if (!$c) {
		df_checkout_session()->setDfCustomer($data);
	}
	else {
		df_customer_info_add($c, $data);
		/**
		 * 2016-08-22
		 * Сохранять покупателя надо обязательно,
		 * потому что при оформлении заказа зарегистрированным ранее покупателем
		 * его учётная запись не пересохраняется.
		 */
		df_eav_partial_save($c);
	}
}


