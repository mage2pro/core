<?php
use Zend\Stdlib\PriorityQueue as Q;
/**
 * 2017-07-07
 * Returns the mamimym priority from the queue.
 * A check for emptiness is required because of «max(): Array must contain at least one element».
 * @used-by df_zf_pq_add_highest()
 * @param Q $q
 * @return int
 */
function df_zf_pq_max(Q $q) {return $q->isEmpty() ? 0 : max($q->toArray(Q::EXTR_PRIORITY));}

/**
 * 2017-07-07
 * Returns the minimum priority from the queue.
 * A check for emptiness is required because of «min(): Array must contain at least one element».
 * @used-by df_zf_pq_add_lowest()
 * @used-by \Df\API\Client::appendFilter()
 * @param Q $q
 * @return int
 */
function df_zf_pq_min(Q $q) {return $q->isEmpty() ? 0 : min($q->toArray(Q::EXTR_PRIORITY));}

/**
 * 2017-07-07 Adds $i to the queue at the lowest priority.
 * @param Q $q
 * @param mixed $i
 */
function df_zf_pq_add_highest(Q $q, $i) {$q->insert($i, df_zf_pq_max($q) + 1);}

/**
 * 2017-07-07 Adds $i to the queue at the lowest priority.
 * @param Q $q
 * @param mixed $i
 */
function df_zf_pq_add_lowest(Q $q, $i) {$q->insert($i, df_zf_pq_min($q) - 1);}