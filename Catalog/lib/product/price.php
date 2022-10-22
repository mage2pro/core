<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Pricing\Price\PriceInterface as IPrice;
/**
 * 2021-12-21
 * @param P $p
 */
function df_price_regular(P $p):float {return df_prices($p)['regular_price'];}

/**
 * 2021-12-21
 * I do not use @see \Magento\Catalog\Model\Product::getSpecialPrice()
 * because it can return an outdated or a future special price:
 * the current time could be not between @see \Magento\Catalog\Model\Product::getSpecialFromDate()
 * and @see \Magento\Catalog\Model\Product::getSpecialToDate()
 * `df_prices($p)['special_price']` returns `false` in this case.
 * @used-by \TFC\GoogleShopping\Att\SalePrice::v() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/4)
 * @param P $p
 * @return float|false
 */
function df_price_special(P $p) {return df_prices($p)['special_price'];}

/**
 * 2021-12-21
 * 1) A simple product with a special price:
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
 * 2) A configurable product withot a special price:
 *	{
 *		"base_price": 0,
 *		"catalog_rule_price": false,
 *		"configured_price": 0,
 *		"configured_regular_price": 0,
 *		"custom_option_price": [],
 *		"final_price": 609.95,
 *		"msrp_price": 0,
 *		"regular_price": 609.95,
 *		"special_price": false,
 *		"tier_price": false,
 *		"wishlist_configured_price": 609.95
 *	}
 * @uses \Magento\Framework\Pricing\Price\PriceInterface::getValue()
 * @uses \Magento\Framework\Pricing\Price\AbstractPrice::getValue()
 * @used-by df_price_regular()
 * @used-by df_price_special()
 * @param P $p
 * @return array(string => IPrice)
 */
function df_prices(P $p):array {return df_map($p->getPriceInfo()->getPrices(), function(IPrice $p) {return $p->getValue();});}