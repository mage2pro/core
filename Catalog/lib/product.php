<?php
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Helper\Product as ProductH;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as Downloadable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

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
 * @param P[] $pp
 * @return P[]
 */
function df_not_configurable(array $pp) {return array_filter($pp, function(P $p) {return
	!df_configurable($p);		
});}

/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\Swatches\Block\Product\Renderer\Configurable::aroundGetAllowProducts()
 * @return ProductH
 */
function df_product_h() {return df_o(ProductH::class);}

/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @param int $id
 * @return P
 */
function df_product_load($id) {$r = df_new_om(P::class); return $r->load($id);}

/**
 * 2017-04-20
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