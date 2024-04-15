<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Exception\NotFoundException as NotFound;
/**
 * 2018-09-27
 * @used-by df_product_current_id()
 * @param Closure|bool|mixed $onError
 * @return P|null
 * @throws NotFound|Exception
 */
function df_product_current($onError = null) {return df_try(function() {return
	# 2019-08-01 `df_catalog_locator()` is available only in the backend.
	# 2024-04-15 https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10
	df_is_backend() && df_catalog_locator_exists()
		? df_catalog_locator()->getProduct()
		: (df_registry('current_product') ?: df_error())
;}, $onError);}

/**
 * 2019-11-15
 * @used-by \Dfe\Markdown\Modifier::modifyData()
 * @return int|null
 */
function df_product_current_id() {return !($p = df_product_current() /** @var P $p */) ? null : $p->getId();}