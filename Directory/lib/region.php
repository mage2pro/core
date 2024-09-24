<?php
use Magento\Customer\Model\Address\AbstractAddress as A;
/**
 * 2019-06-13
 * @used-by KingPalm\B2B\Observer\RegisterSuccess::execute()
 */
function df_region_name(A $a):string {return df_ets($a->getRegion() ?: $a->getRegionModel($a->getRegionId())->getName());}
