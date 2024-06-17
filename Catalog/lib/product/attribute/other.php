<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2021-04-24
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 */
function df_product_att_changed(P $p, string $k):bool {return $p->getStoreId() ? !is_null($p[$k]) : $p->dataHasChangedFor($k);}