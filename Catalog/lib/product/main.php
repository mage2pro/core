<?php
use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepository;
use Magento\Catalog\Helper\Product as ProductH;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NotFoundException as NotFound;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2019-02-26
 * 2019-05-15 I have added the $s parameter: https://magento.stackexchange.com/a/177164 
 * @see df_category()
 * @see df_product_load()
 * @used-by ikf_product_printer()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::product()
 * @used-by \Inkifi\Mediaclip\Event::product()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
 * @used-by \Inkifi\Mediaclip\T\CaseT\Product::t02()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @param int|string|P|OI $p
 * @param int|string|null|bool|IStore $s [optional]
 * @return P
 */
function df_product($p, $s = false) {return $p instanceof P ? $p : call_user_func(
	/** @uses \Magento\Catalog\Model\ProductRepository::get() */
	/** @uses \Magento\Catalog\Model\ProductRepository::getById() */
	[df_product_r(), ctype_digit($p) || df_is_oi($p) ? 'getById' : 'get']
	,df_is_oi($p) ? $p->getProductId() : $p
	,false
	,false === $s ? null : df_store_id(true === $s ? null : $s)
	,true === $s
);}

/**
 * 2018-09-27
 * @param \Closure|bool|mixed $onError
 * @return P|null
 * @throws NotFound|\Exception
 */
function df_product_current($onError = null) {return df_try(function() {return
	df_is_backend() ? df_catalog_locator()->getProduct() :
		(df_registry('current_product') ?: df_error())
;}, $onError);}

/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\Swatches\Block\Product\Renderer\Configurable::aroundGetAllowProducts()
 * @return ProductH
 */
function df_product_h() {return df_o(ProductH::class);}

/**
 * 2018-06-04
 * @see df_product()
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @param int $id
 * @return P
 */
function df_product_load($id) {return df_product_r()->getById($id, false, null, true);}

/**
 * 2019-02-26                
 * @see df_category_r()
 * @used-by df_product()
 * @used-by df_product_load()
 * @return IProductRepository|ProductRepository
 */
function df_product_r() {return df_o(IProductRepository::class);}