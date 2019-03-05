<?php
use Magento\Framework\Api\AbstractExtensibleObject as O;
use Magento\Framework\Api\AttributeInterface as IA;
use Magento\Framework\Api\AttributeValue as A;
/**
 * 2019-03-06
 * @used-by \Verdepieno\Core\CustomerAddressForm::f()
 * @param O $o
 * @param string $k
 * @return mixed|null
 */
function df_cav(O $o, $k) {
	$a = $o->getCustomAttribute($k); /** @var IA|A $a */
	return $a ? $a->getValue() : null;
}