<?php
use Magento\Catalog\Model\Product as P;
use Magento\Review\Model\Review\Summary as RS;
/**
 * 2019-11-20
 * 2022-10-22 @deprecated It is unused.
 * @param P|int $p
 * @return RS
 */
function df_review_summary($p) {
	$r = df_new_om(RS::class); /** @var RS $r */
	$r->load(df_product_id($p));
	return $r;
}