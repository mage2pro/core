<?php
/**
 * 2015-12-25
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 */
function df_n_prepend(string $s):string {return df_es($s) ? $s : "\n$s";}