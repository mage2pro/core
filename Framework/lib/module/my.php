<?php
/**
 * 2020-04-16
 * @see dfe_modules()
 * @used-by \Df\Framework\Plugin\Css\PreProcessor\File\FileList\Collator::afterCollate()
 * @return string[]
 */
function df_modules_my():array {return dfcf(function() {return array_keys(array_filter(df_map_k(
	df_map_r(df_module_list()->getNames(), function(string $m):array {return [$m,
		df_starts_with($m, 'Magento_') ? [] : df_package($m, 'authors', [])
	];})
	,function(string $m, array $authors):bool {return
		df_starts_with($m, ['Df_', 'Dfe_']) || df_find($authors, function(array $a) {return
			'admin@mage2.pro' === dfa($a, 'email')
		;})
	;}
)));});}