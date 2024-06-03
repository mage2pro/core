<?php
use Df\Config\ArrayItem as AI;
use Magento\Framework\DataObject as _DO;

/**
 * 2016-07-31
 * 2024-06-03
 * We still can not use «Union Types» (e.g. `callable|iterable`) because they require PHP ≥ 8 (we need to support PHP ≥ 7.1):
 * https://php.watch/versions/8.0/union-types
 * https://3v4l.org/AOWmO
 * @uses df_id()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @param Traversable|array(int|string => _DO|AI) $c
 * @return int[]|string[]
 */
function dfa_ids($c):array {return df_map('df_id', $c);}