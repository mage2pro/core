<?php
namespace Df\API\Response;
use Df\API\Client;
use Df\Core\Exception as DFE;
/**
 * 2017-05-07
 * @see \Dfe\Dynamics365\API\Validator\JSON
 */
interface IValidator {
	/**
	 * 2017-05-07
	 * @used-by \Df\API\Client::p()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::validate()
	 * @param Client $c
	 * @param string $r
	 * @throws DFE
	 */
	function validate(Client $c, $r);
}