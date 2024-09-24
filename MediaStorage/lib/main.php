<?php
use Magento\MediaStorage\Model\File\Storage\Response as Resp;
/**
 * 2020-12-13
 * @used-by TFC\Core\Plugin\MediaStorage\App\Media::aroundLaunch()
 */
function df_file_resp():Resp {return df_o(Resp::class);}