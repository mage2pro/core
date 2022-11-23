<?php
use Df\Framework\Api\AbstractSimpleObject as DFO;
use Magento\Framework\Api\AbstractSimpleObject as O;
/**
 * 2017-05-22
 * @return mixed|null
 */
function df_api_object_get(O $o, string $k) {return DFO::get($o, $k);}