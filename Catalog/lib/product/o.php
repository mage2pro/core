<?php
use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepository;
use Magento\Catalog\Helper\Product as ProductH;
use Magento\Catalog\Model\ProductRepository;

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