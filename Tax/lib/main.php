<?php
use Magento\Sales\Model\Order as O;
/**
 * 2017-09-30
 * @used-by df_oqi_tax_rate()
 * @used-by df_tax_rate_shipping()
 * @param float $withTax
 * @param float $withoutTax
 * @return float
 */
function df_tax_rate($withTax, $withoutTax) {return 100 * ($withTax - $withoutTax) / $withoutTax;}

/**
 * 2017-09-30
 * @used-by \Dfe\YandexKassa\Charge::pTax()
 * @param O $o
 * @return float
 */
function df_tax_rate_shipping(O $o) {return df_tax_rate($o->getShippingInclTax(), $o->getShippingAmount());}