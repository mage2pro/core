<?php
use Laminas\Stdlib\PriorityQueue as Q;
/**
 * 2017-07-07
 * Returns the mamimym priority from the queue.
 * A check for emptiness is required because of «max(): Array must contain at least one element».
 * @used-by df_zf_pq_add_highest()
 */
function df_zf_pq_max(Q $q):int {return $q->isEmpty() ? 0 : max($q->toArray(Q::EXTR_PRIORITY));}

/**
 * 2017-07-07
 * Returns the minimum priority from the queue.
 * A check for emptiness is required because of «min(): Array must contain at least one element».
 * @used-by df_zf_pq_add_lowest()
 * @used-by Df\API\Client::appendFilter()
 */
function df_zf_pq_min(Q $q):int {return $q->isEmpty() ? 0 : min($q->toArray(Q::EXTR_PRIORITY));}

/**
 * 2017-07-07 Adds $i to the queue at the lowest priority.
 * @used-by Df\Zf\Test\main::t01()
 * @param mixed $i
 */
function df_zf_pq_add_highest(Q $q, $i):void {$q->insert($i, df_zf_pq_max($q) + 1);}

/**
 * 2017-07-07 Adds $i to the queue at the lowest priority.
 * @used-by Df\Zf\Test\main::t01()
 * @param mixed $i
 */
function df_zf_pq_add_lowest(Q $q, $i):void {$q->insert($i, df_zf_pq_min($q) - 1);}