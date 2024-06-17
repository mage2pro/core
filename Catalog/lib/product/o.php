<?php
use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepository;
use Magento\Catalog\Helper\Product as ProductH;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product as Res;
use Magento\Catalog\Model\ResourceModel\Product\Action;

/**
 * 2019-09-22 «Best way to update product's attribute value»: https://magento.stackexchange.com/a/157446
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 */
function df_product_action():Action {return df_o(Action::class);}

/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\Swatches\Block\Product\Renderer\Configurable::aroundGetAllowProducts()
 */
function df_product_h():ProductH {return df_o(ProductH::class);}

/**
 * 2019-02-26
 * @see df_category_r()
 * @used-by df_product()
 * @used-by df_product_load()
 * @used-by \CanadaSatellite\Theme\Plugin\Model\LinkManagement::aroundSaveChild(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/44)
 * @used-by \PPCs\Core\Plugin\Iksanika\Stockmanage\Controller\Adminhtml\Product\MassUpdateProducts::beforeExecute()
 * @return IProductRepository|ProductRepository
 */
function df_product_r() {return df_o(IProductRepository::class);}

/**
 * 2019-09-22
 * @used-by df_product_sku2id()
 */
function df_product_res():Res {return df_o(Res::class);}