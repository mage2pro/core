<?php
use Magento\Eav\Model\Entity\Type as T;
/**
 * 2024-05-23 "Implement `df_eav_type()`": https://github.com/mage2pro/core/issues/388
 */
function df_eav_type(string $t):T {return df_eav_config()->getEntityType($t);}