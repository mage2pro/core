<?php
use Magento\Framework\View\DesignInterface as IDesign;
use Magento\Framework\View\Design\Theme\ResolverInterface as IThemeResolver;
use Magento\Theme\Model\ResourceModel\Theme\Collection as ThemeCollection;
use Magento\Theme\Model\Theme;
use Magento\Theme\Model\Theme\Resolver as ThemeResolver;
use Magento\Theme\Model\View\Design;
/**
 * 2016-11-29
 * @used-by df_theme()
 * @used-by \Wolf\Filter\Block\Navigation::getCacheKeyInfo()
 * @return IDesign|Design
 */
function df_design() {return df_o(IDesign::class);}

/**
 * 2016-11-29
 * 2022-11-21 @deprecated It is unused.
 * @param int|null $id [optional]
 * @return Theme|null
 */
function df_theme($id = null) {/** @var Theme|null $r */
	if ($id) {
		$r = df_themes()->getItemById($id);
	}
	else {
		$r = df_theme_resolver()->get();
		if ($r->isVirtual()) {
			$r = df_themes()->getItemById(df_design()->getConfigurationDesignTheme('frontend'));
		}
	}
	return $r;
}

/**
 * 2016-11-29
 * @return IThemeResolver|ThemeResolver
 */
function df_theme_resolver() {return df_o(IThemeResolver::class);}

/**
 * 2016-11-29
 * @used-by df_theme()
 */
function df_themes():ThemeCollection {return df_o(ThemeCollection::class);}