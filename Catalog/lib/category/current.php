<?php
use Magento\Catalog\Model\Category as C;
use Magento\Framework\Exception\NotFoundException as NotFound;
/**
 * 2024-04-15
 * 2024-04-20 "Implement `df_category_current()`" https://github.com/mage2pro/core/issues/363
 * @param Closure|bool|mixed $onError
 * @return C|null
 * @throws NotFound|Exception
 */
function df_category_current($onError = null) {return df_try(function() {return
	null
;}, $onError);}