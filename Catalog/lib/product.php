<?php
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepository;
use Magento\Catalog\Helper\Product as ProductH;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as Downloadable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2016-05-01
 * How to programmatically detect whether a product is configurable?
 * https://mage2.pro/t/1501    
 * @used-by df_not_configurable()
 * @param P $p
 * @return bool
 */
function df_configurable(P $p) {return Configurable::TYPE_CODE === $p->getTypeId();}

/**           
 * 2018-09-02
 * @used-by df_wishlist_item_candidates()
 * @param P[] $pp
 * @return P[]
 */
function df_not_configurable(array $pp) {return array_filter($pp, function(P $p) {return
	!df_configurable($p);		
});}

/**
 * 2019-02-26
 * 2019-05-15 I have added the $s parameter: https://magento.stackexchange.com/a/177164
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
function df_product($p, $s = false) {return $p instanceof P ? $p : df_product_r()->getById(
	df_is_oi($p) ? $p->getProductId() : $p
	,false
	,false === $s ? null : df_store_id(true === $s ? null : $s)
	,true === $s
);}

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
 * @used-by df_product()
 * @used-by df_product_load()
 * @return IProductRepository|ProductRepository
 */
function df_product_r() {return df_o(IProductRepository::class);}

/**
 * 2017-04-20
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @param string $type
 * @return bool
 */
function df_product_type_composite($type) {return in_array($type, [
	Bundle::TYPE_CODE, Configurable::TYPE_CODE, Grouped::TYPE_CODE
]);}

/**
 * 2015-11-14
 * @param P $p
 * @return bool
 */
function df_virtual_or_downloadable(P $p) {return in_array(
	$p->getTypeId(), [Type::TYPE_VIRTUAL, Downloadable::TYPE_DOWNLOADABLE]
);}