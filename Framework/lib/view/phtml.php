<?php
use Magento\Framework\View\Design\FileResolution\Fallback\TemplateFile as Resolver;
/**
 * 2017-05-11
 * @used-by \Dfe\Portal\Router::match()
 * @param string|object $m
 */
function df_phtml_exists(string $path, $m):bool {
	$d = ['module' => ($m = df_module_name($m))]; /** @var array(string => mixed) $d */
	df_asset()->updateDesignParams($d);
	return !!df_phtml_resolver()->getFile($d['area'], $d['themeModel'], $path, $m);
}

/**
 * 2017-05-11
 * @used-by df_phtml_exists()
 */
function df_phtml_resolver():Resolver {return df_o(Resolver::class);}