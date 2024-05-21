<?php
use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepository;
use Magento\Catalog\Helper\Product as ProductH;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product as Res;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Api\Data\StoreInterface as IStore;
/**
 * 2019-02-26
 * 2019-05-15 I have added the $s parameter: https://magento.stackexchange.com/a/177164
 * 2019-09-20
 * I tried to support SKU as $p using the following way:
 *	call_user_func(
 *		[df_product_r(), ctype_digit($p) || df_is_oi($p) ? 'getById' : 'get']
 *		,df_is_oi($p) ? $p->getProductId() : $p
 *		...
 *	)
 * https://github.com/mage2pro/core/commit/01d4fbbf83
 * It was wrong because SKU can be numeric, so the method become ambiguous.
 * Use @see \Magento\Catalog\Model\ProductRepository::get() directly to load a product by SKU, e.g.:
 * 		df_product_r()->get('your SKU')
 * @see df_category()
 * @see df_product_load()
 * @used-by df_category_names()
 * @used-by ikf_product_printer()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::product()
 * @used-by \Inkifi\Mediaclip\Event::product()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
 * @used-by \Inkifi\Mediaclip\T\CaseT\Product::t02()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @param int|string|P|OI|QI $p
 * @param int|string|null|bool|IStore $s [optional]
 * @throws NSE
 */
function df_product($p, $s = false):P {return $p instanceof P ? $p : df_product_r()->getById(
	/**
	 * 2020-02-05
	 * 1) I do not use @see \Magento\Sales\Model\Order\Item::getProduct()
	 * and @see \Magento\Quote\Model\Quote\Item\AbstractItem::getProduct() here,
	 * because they return `null` for an empty product ID, but df_product() should throw @see NSE in such cases.
	 * 2) Also, my implementation allows to specify a custom $s.
	 */
	df_is_oqi($p) ? $p->getProductId() : $p
	,false
	,false === $s ? null : df_store_id(true === $s ? null : $s)
	,true === $s
);}

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
 * 2019-11-18
 * @see df_category_id()
 * @used-by df_qty()
 * @used-by df_review_summary()
 * @param P|int $p
 */
function df_product_id($p):int {return df_int($p instanceof P ? $p->getId() : $p);}

/**
 * 2018-06-04
 * @see df_product()
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 */
function df_product_load(int $id):P {return df_product_r()->getById($id, false, null, true);}

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