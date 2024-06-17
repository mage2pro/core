<?php
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\ResourceModel\Product as Res;
use Magento\Catalog\Model\ResourceModel\Product\Action;

/**
 * 2019-09-22 «Best way to update product's attribute value»: https://magento.stackexchange.com/a/157446
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 */
function df_product_action():Action {return df_o(Action::class);}

/**             
 * 2019-11-18
 * @see df_category_id()
 * @used-by df_qty()
 * @used-by df_review_summary()
 * @param P|int $p
 */
function df_product_id($p):int {return df_int(df_is_p($p) ? $p->getId() : $p);}

/**
 * 2018-06-04
 * @see df_product()
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 */
function df_product_load(int $id):P {return df_product_r()->getById($id, false, null, true);}

/**
 * 2019-09-22
 * @used-by df_product_sku2id()
 */
function df_product_res():Res {return df_o(Res::class);}