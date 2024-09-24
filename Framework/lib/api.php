<?php
use Df\Framework\Api\AbstractSimpleObject as DFO;
use Magento\Framework\Api\AbstractSimpleObject as O;
use Magento\Framework\Api\AbstractSimpleObject as oAPI;

/**
 * 2017-05-22
 * @used-by Df\Framework\Plugin\Reflection\DataObjectProcessor::aroundBuildOutputDataArray()
 * @return mixed|null
 */
function df_api_o_get(O $o, string $k) {return DFO::get($o, $k);}

/**
 * 2023-07-29
 * @used-by df_gd()
 * @used-by df_has_gd()
 * @param mixed $v
 */
function df_is_api_o($v):bool {return $v instanceof oAPI;}