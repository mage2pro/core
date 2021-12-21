<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Pricing\Price\PriceInterface as IPrice;
/**
 * 2021-12-21
 *	{
 *		"base_price": 39.95,
 *		"catalog_rule_price": false,
 *		"configured_price": 39.95,
 *		"configured_regular_price": 79.95,
 *		"custom_option_price": [],
 *		"final_price": 39.95,
 *		"msrp_price": 39.95,
 *		"regular_price": 79.95,
 *		"special_price": 39.95,
 *		"tier_price": false,
 *		"wishlist_configured_price": 39.95
 *	}
 * @param P $p
 * @return array(string => IPrice)
 */
function df_prices(P $p) {return df_map($p->getPriceInfo()->getPrices(), function(IPrice $p) {return $p->getValue();});}