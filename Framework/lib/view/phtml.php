<?php
use Magento\Framework\View\Design\FileResolution\Fallback\TemplateFile as Resolver;
/**
 * 2017-05-11
 * @param string $path
 * @param string|object $module
 * @return bool
 */
function df_phtml_exists($path, $module) {
	$module = df_module_name($module);
	$params = ['module' => $module];
	df_asset()->updateDesignParams($params);
	return !!df_phtml_resolver()->getFile($params['area'], $params['themeModel'], $path, $module);
}

/**
 * 2017-05-11
 * @used-by df_phtml_exists()
 * @return Resolver
 */
function df_phtml_resolver() {return df_o(Resolver::class);}
