<?php
use Magento\Framework\View\Design\FileResolution\Fallback\TemplateFile as Resolver;
/**
 * 2023-08-01
 * @used-by df_bt_entry_is_phtml()
 * @used-by Df\Qa\Trace\Frame::isPHTML()
 */
function df_is_phtml(string $f):bool {return df_ends_with($f, '.phtml');}

/**
 * 2023-08-01
 * @used-by df_block()
 * @used-by df_phtml_exists()
 */
function df_phtml_add_ext(string $f):string {return df_file_ext_add($f, 'phtml');}

/**
 * 2017-05-11
 * @used-by Dfe\Portal\Router::match()
 * @param string|object $m
 */
function df_phtml_exists(string $path, $m):bool {
	$d = ['module' => ($m = df_module_name($m))]; /** @var array(string => mixed) $d */
	df_asset()->updateDesignParams($d);
	return !!df_phtml_resolver()->getFile($d['area'], $d['themeModel'], df_phtml_add_ext($path), $m);
}

/**
 * 2017-05-11
 * @used-by df_phtml_exists()
 */
function df_phtml_resolver():Resolver {return df_o(Resolver::class);}