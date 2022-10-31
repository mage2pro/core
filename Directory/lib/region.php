<?php
use Magento\Customer\Model\Address\AbstractAddress as A;
/**
 * 2019-06-13
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 * @param string|null $name
 * @param int|null $id
 * @return string|null
 */
function df_region_name($name, $id) {
	$r = null; /** @var string $r */
	if ($name) {
		$r = $name;
	}
	elseif ($id) {
		$a = df_new_om(A::class); /** @var A $a */
		$r = $a->getRegionModel((int)$id)->getName();
	}
	return $r;
}
