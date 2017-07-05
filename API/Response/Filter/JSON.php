<?php
namespace Df\API\Response\Filter;
// 2017-07-05
/** @used-by \Dfe\Dynamics365\API\Client\JSON::responseFilterC() */
final class JSON implements \Df\API\Response\IFilter {
	/**
	 * 2017-07-05
	 * @override
	 * @see \Df\API\Response\IProcessor::filter()
	 * @used-by \Df\API\Client::p()
	 * @param string $response
	 * @return string|mixed
	 */
	function filter($response) {return df_ksort_r(df_json_decode($response));}
}