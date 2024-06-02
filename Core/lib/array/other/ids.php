<?php
/**
 * 2016-07-31
 * @uses df_id()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @param Traversable|array(int|string => _DO|AI) $c
 * @return int[]|string[]
 */
function dfa_ids($c):array {return df_map('df_id', $c);}