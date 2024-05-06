<?php
use Magento\Cms\Model\Template\FilterProvider as FP;
/**
 * 2024-03-25
 * @used-by df_cms_filter_page()
 */
function df_cms_filter_provider():FP {return df_o(FP::class);}

/**
 * 2024-03-25
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::l3a() (https://github.com/cabinetsbay/site/issues/98)
 */
function df_cms_filter_page(string $s):string {return df_cms_filter_provider()->getPageFilter()->filter($s);}