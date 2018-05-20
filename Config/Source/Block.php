<?php
namespace Df\Config\Source;
use Magento\Cms\Model\Block as B;
/**
 * 2018-05-21
 * @see \Magento\Cms\Model\Config\Source\Page
 * @used-by AlbumEnvy_Popup
 */
final class Block extends \Df\Config\Source {
	/**
	 * 2018-05-21
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return df_map_0(df_sort_a(df_map_r(df_cms_blocks(), function(B $b) {return [
		$b->getId(), $b->getTitle()
	];})), '-- select a CMS block --');}
}