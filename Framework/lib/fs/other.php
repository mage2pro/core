<?php
use Magento\Framework\Filesystem\Io\File as File;

/**
 * 2017-04-03 Портировал из РСМ. Никем не используется.
 * 2022-11-03 @deprecated It is unused.
 */
function df_fs_delete(string $p):void {File::rmdirRecursive(df_param_sne($p, 0));}



