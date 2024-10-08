<?php
use Magento\Sales\Model\Order as O;
/**
 * 2017-09-30
 * 2017-11-03
 * «Division by zero in mage2pro/core/Tax/lib/main.php on line 11»
 * https://github.com/mage2pro/core/issues/42
 * It is normal for an order position to have zero price: e.g., in case of free shipping.
 * @used-by df_oqi_tax_rate()
 * @used-by df_tax_rate_shipping()
 */
function df_tax_rate(float $withTax, float $withoutTax):float {return !$withoutTax ? 0 :
	100 * ($withTax - $withoutTax) / $withoutTax
;}

/**
 * 2017-09-30
 * @used-by Dfe\YandexKassa\Charge::pTaxLeafs()
 */
function df_tax_rate_shipping(O $o):float {return df_tax_rate($o->getShippingInclTax(), $o->getShippingAmount());}