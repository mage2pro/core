<?php
use Magento\Cms\Model\Template\FilterProvider as FP;
/**
 * 2024-03-25
 * 2024-05-06 https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Cms/Model/Template/FilterProvider.php
 * @used-by df_cms_filter_page()
 */
function df_cms_filter_provider():FP {return df_o(FP::class);}

/**
 * 2024-03-25
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::l3a() (https://github.com/cabinetsbay/site/issues/98)
 */
function df_cms_filter_page(string $s):string {return df_cms_filter_provider()->getPageFilter()->filter($s);}