<?php
use Magento\Framework\View\Design\FileResolution\Fallback\TemplateFile as Resolver;
/**
 * 2017-05-11
 * @used-by \Dfe\Portal\Router::match()
 * @param string|object $m
 */
function df_phtml_exists(string $path, $m):bool {
	$m = df_module_name($m);
	$params = ['module' => $m]; /** @var array(string => mixed) $params */
	df_asset()->updateDesignParams($params);
	return !!df_phtml_resolver()->getFile($params['area'], $params['themeModel'], $path, $m);
}

/**
 * 2017-05-11
 * @used-by df_phtml_exists()
 */
function df_phtml_resolver():Resolver {return df_o(Resolver::class);}