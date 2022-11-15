<?php
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Type\AbstractType as T;
use Magento\Wishlist\Model\Item as I;
/**
 * 2018-09-02
 * 1) I have implemented it by analogy with:
 * 1.1) @see \Magento\Wishlist\Model\Item::addToCart():
 *		$buyRequest = $this->getBuyRequest();
 * 		$cart->addProduct($product, $buyRequest);  
 * https://github.com/magento/magento2/blob/2.2.5/app/code/Magento/Wishlist/Model/Item.php#L434-L436
 * @see \Magento\Checkout\Model\Cart::addProduct():
 * 1.2) \Magento\Quote\Model\Quote::addProduct():
 * 		$cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, $processMode);
 * https://github.com/magento/magento2/blob/2.2.5/app/code/Magento/Quote/Model/Quote.php#L1606
 * 2) If the wishlist item product is configurable, then
 * @uses \Magento\ConfigurableProduct\Model\Product\Type\Configurable::_prepareProduct()
 * includes the configurable product as the first element of the result's array.
 * We do not need it, so we filter it out.
 * @used-by frugue/core/view/frontend/templates/wishlist/item/column/image.phtml
 * @return P[]|string
 */
function df_wishlist_item_candidates(I $i) {return df_not_configurable(df_assert_array(
	$i->getProduct()->getTypeInstance()->prepareForCartAdvanced($i->getBuyRequest(), $i->getProduct(), T::PROCESS_MODE_LITE)
));}