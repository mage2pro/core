<?php
/**
 * 2015-12-01 Отныне всегда используем `/` вместо @see DIRECTORY_SEPARATOR
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * 2024-06-09 "`df_cc_path()` should trim internal `/` and `DS` for arguments": https://github.com/mage2pro/core/issues/406
 * @used-by df_config_e()
 * @used-by df_db_credentials()
 * @used-by df_fs_etc()
 * @used-by df_img_resize()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_module_name_by_path()
 * @used-by df_path_abs()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @used-by \CabinetsBay\Catalog\B\Category::images() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Df\API\Client::url()
 * @used-by \Df\API\Facade::path()
 * @used-by \Df\Config\Backend::value()
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfe\Portal\Router::match()
 * @used-by \Dfe\Sift\API\Facade\GetDecisions::path()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by \Inkifi\Mediaclip\API\Client::urlBase()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 * @used-by \TFC\Image\Command\C1::image()
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @param string|string[] ...$a
 */
function df_cc_path(...$a):string {
	$a = df_clean(dfa_flatten($a));
	$s = implode($a); /** @var string $s */
	$a = df_trim_ds($a);
	return implode('/', $a);
}

/**
 * 2016-05-31
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * 2023-01-01 @deprecated It is unused.
 * @param string|string[] ...$a
 */
function df_cc_path_t(...$a):string {return df_append(df_cc_path(dfa_flatten($a)), '/');}