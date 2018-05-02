<?php
use Df\Sales\Setup\UpgradeSchema as Schema;
use Magento\Sales\Model\Order as O;
use Magento\Framework\DataObject;

/**
 * 2018-05-01
 * @see df_ci_add()
 * @used-by df_oi_save()
 * @used-by \Doormall\Shipping\Observer\QuoteSubmitBefore::execute()
 * @param DataObject|O $o
 * @param array(string => mixed) $info
 */
function df_oi_add(DataObject $o, array $info) {
	$o[Schema::F__DF] = df_json_encode(df_extend(df_eta(df_oi_get(null, $o)), $info));
}

/**
 * 2018-05-01
 * @see df_ci_get()
 * @used-by df_oi_add()
 * @used-by \Doormall\Shipping\Plugin\Sales\Model\Order::afterGetShippingDescription()
 * @param string|object|null $m [optional]
 * @param DataObject|O|null $o [optional]
 * @return string|array(string => mixed)|null
 */
function df_oi_get($m = null, DataObject $o = null) {
	$o = df_order($o) ?: $o;
	return !$o ? null : dfak(df_eta(df_json_decode($o[Schema::F__DF])), !$m ? null : df_class_second_lc($m));
}

/**
 * 2018-05-01
 * @see df_ci_save()
 * @param string|object $m
 * @param mixed|null $info
 * @param O|null $o [optional]
 */
function df_oi_save($m, $info, O $o = null) {
	$data = [df_class_second_lc($m) => $info]; /** @var array(string => string) $data */
	$o = df_order($o);
	df_oi_add($o, $data);
	$o->save();
}