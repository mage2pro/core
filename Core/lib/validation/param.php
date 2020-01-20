<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
/**
 * @param int|float  $v
 * @param int $ord	zero-based
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $sl [optional]
 * @return int|float
 * @throws DFE
 */
function df_param_between($v, $ord, $min = null, $max = null, $sl = 0) {return Q::assertParamIsBetween(
	$v, $ord, $min, $max, ++$sl
);}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param float $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return float
 * @throws DFE
 */
function df_param_float($v, $ord, $sl = 0) {return Q::assertParamIsFloat($v, $ord, ++$sl);}

/**
 * @param int $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return int
 * @throws DFE
 */
function df_param_integer($v, $ord, $sl = 0) {return Q::assertParamIsInteger($v, $ord, ++$sl);}

/**
 * @used-by df_country_ctn()
 * @param string $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_param_iso2($v, $ord, $sl = 0) {return Q::assertParamIsIso2($v, $ord, ++$sl);}

/**
 * 2017-04-22
 * @used-by df_file_write()
 * @param string $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_param_s($v, $ord, $sl = 0) {$sl++;
	// Раньше тут стояло:
	// $method->assertParamIsString($v, $ord, $sl)
	// При второй попытке тут стояло if (!$v), что тоже неправильно,
	// ибо непустая строка '0' не проходит такую валидацию.
	return Q::assertValueIsString($v, $sl) ? $v : Q::raiseErrorParam(__FUNCTION__, $ms = [Q::S], $ord, $sl);
}

/**
 * @param string $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_param_sne($v, $ord, $sl = 0) {$sl++;
	// Раньше тут стояло:
	// $method->assertParamIsString($v, $ord, $sl)
	// При второй попытке тут стояло if (!$v), что тоже неправильно,
	// ибо непустая строка '0' не проходит такую валидацию.
	Q::assertValueIsString($v, $sl);
	return '' !== strval($v) ? $v : Q::raiseErrorParam(__FUNCTION__, $ms = [Q::NES], $ord, $sl);
}