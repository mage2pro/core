<?php
namespace Df\API\Response;
use Df\Core\Exception as DFE;
// 2017-05-07
/**  @see \Df\API\Response\Filter\JSON */
interface IFilter {
	/**
	 * 2017-05-07
	 * @used-by \Df\API\Client::p()
	 * @param string $r
	 * @return string|mixed
	 */
	function filter($r);
}