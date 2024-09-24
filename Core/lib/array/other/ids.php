<?php
use Df\Config\ArrayItem as AI;
use Magento\Framework\DataObject as _DO;

/**
 * 2016-07-31
 * 2024-06-03
 * 1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 3) https://php.net/manual/en/language.types.iterable.php
 * @uses df_id()
 * @used-by Df\Config\Backend\ArrayT::processI()
 * @param Traversable|array(int|string => _DO|AI) $c
 * @return int[]|string[]
 */
function dfa_ids(iterable $c):array {return df_map('df_id', $c);}