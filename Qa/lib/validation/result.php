<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;

/**
 * @used-by df_db_column_describe()
 * @used-by \Df\GoogleFont\Fonts::responseA()
 * @used-by \Df\Xml\X::asCanonicalArray()
 * @used-by \Dfe\FacebookLogin\Customer::responseJson()
 * @param array $v
 * @param int $sl [optional]
 * @return array
 * @throws DFE
 */
function df_result_array($v, $sl = 0) {return Q::assertResultIsArray($v, ++$sl);}

/**
 * Раньше тут стояло: Q::assertResultIsString($v, ++$sl)
 * @used-by \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @see df_assert_sne()
 * @see df_param_sne()
 * @see df_result_sne()
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_result_s($v, $sl = 0) {return df_check_s($v) ? $v : Q::raiseErrorResult(
	__FUNCTION__, [sprintf('A string is required, but got %s.', df_type($v))], ++$sl
);}

/**
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_result_sne($v, $sl = 0) {$sl++;
	df_result_s($v, $sl);
	# Раньше тут стояло:
	# Q::assertResultIsString($v, $sl)
	# При второй попытке тут стояло if (!$v), что тоже неправильно,
	# ибо непустая строка '0' не проходит такую валидацию.
	return '' !== strval($v) ? $v : Q::raiseErrorResult(__FUNCTION__, [Q::NES], $sl);
}