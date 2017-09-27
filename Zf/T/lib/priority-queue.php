<?php
namespace Df\Zf\T\lib;
use Zend\Stdlib\PriorityQueue as Q;
// 2017-07-07
class main extends \Df\Core\TestCase {
	/** @test 2017-07-07 */
	function t00() {}

	/** @test 2017-07-07 */
	function t01() {
		/** @var Q $q */
		$q = new Q;
		$q->insert('value 2', 2);
		$q->insert('value 1', 1);
		$q->insert('value 3', 3);
		$q->insert('value 5', 0);
		df_zf_pq_add_lowest($q, 'value 4');
		df_zf_pq_add_highest($q, 'value 6');
		print_r(df_zf_pq_max($q) . "\n");
		print_r(df_zf_pq_min($q) . "\n");
	}
}