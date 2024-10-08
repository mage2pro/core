<?php
/**
 * 2024-06-09 "Implement `df_modules()": https://github.com/mage2pro/core/issues/413
 * @used-by df_modules_my()
 * @used-by df_modules_p()
 * @return string[]
 */
function df_modules():array {return df_module_list()->getNames();}

/**
 * 2020-04-16
 * @see dfe_modules()
 * @used-by Df\Framework\Plugin\Css\PreProcessor\File\FileList\Collator::afterCollate()
 * @return string[]
 */
function df_modules_my():array {return dfcf(function() {return array_keys(array_filter(df_map_k(
	df_map_r(df_modules(), function(string $m):array {return [$m,
		df_starts_with($m, 'Magento_') ? [] : df_package($m, 'authors', [])
	];})
	,function(string $m, array $authors):bool {return
		df_starts_with($m, ['Df_', 'Dfe_']) || df_find($authors, function(array $a) {return
			'admin@mage2.pro' === dfa($a, 'email')
		;})
	;}
)));});}

/**
 * 2017-06-21
 * @used-by dfe_modules()
 * @return string[]
 */
function df_modules_p(string $p):array {return dfcf(function($p) {return df_sort(array_filter(
	df_modules(), function(string $m) use($p):bool {return df_starts_with($m, $p);}
));}, [$p]);}