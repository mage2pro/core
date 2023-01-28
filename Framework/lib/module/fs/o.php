<?php
use Magento\Framework\Module\Dir\Reader;

/**
 * 2019-12-31
 * @used-by df_module_dir()
 */
function df_module_dir_reader():Reader {return df_o(Reader::class);}