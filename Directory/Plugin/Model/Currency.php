<?php
namespace Df\Directory\Plugin\Model;
use Magento\Directory\Model\Currency as Sb;
use Magento\Framework\App\Cache\Type\Block as B;
use Magento\PageCache\Model\Cache\Type as F;
// 2018-07-03
final class Currency {
	/**
	 * 2018-07-03
	 * "Auto-update the product carousel on a currency rates change
	 * (Magento 2, PlazaThemes Grand theme)": https://github.com/sayitwithagift/core/issues/5
	 * @see \Magento\Directory\Model\Currency::saveRates()
	 * @param Sb $sb
	 * @return $sb
	 */
	function afterSaveRates(Sb $sb) {
		df_cache_clean_types(B::TYPE_IDENTIFIER, F::TYPE_IDENTIFIER);
		return $sb;
	}
}