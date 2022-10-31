<?php
namespace Df\Directory\Plugin\Model;
use Magento\Directory\Model\Currency as Sb;
# 2018-07-03
final class Currency {
	/**
	 * 2018-07-03
	 * "Auto-update the product carousel on a currency rates change
	 * (Magento 2, PlazaThemes Grand theme)": https://github.com/sayitwithagift/core/issues/5
	 * @see \Magento\Directory\Model\Currency::saveRates()
	 * @param Sb $sb
	 */
	function afterSaveRates(Sb $sb):Sb {
		df_cache_clean_blocks();
		df_cache_clean_pages();
		return $sb;
	}
}