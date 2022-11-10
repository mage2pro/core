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
 * @throws DFE
 */
function df_result_array($v, $sl = 0):array {return Q::assertResultIsArray($v, ++$sl);}

/**
 * Раньше тут стояло: Q::assertResultIsString($v, ++$sl)
 * @used-by \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @see df_assert_sne()
 * @see df_param_sne()
 * @see df_result_sne()
 * @param string $v
 * @param int $sl [optional]
 * @throws DFE
 */
function df_result_s($v, $sl = 0):string {return df_check_s($v) ? $v : Q::raiseErrorResult(
	__FUNCTION__, [sprintf('A string is required, but got %s.', df_type($v))], ++$sl
);}

/**
 * @used-by df_country_2_to_3()
 * @used-by df_country_3_to_2()
 * @used-by df_dts()
 * @used-by \Df\API\Settings::merchantID()
 * @used-by \Df\GingerPaymentsBase\Method::option()
 * @used-by \Df\Payment\Operation::id()
 * @used-by \Df\Payment\Operation\Source::customerEmail()
 * @used-by \Df\Payment\Operation\Source\Creditmemo::id()
 * @used-by \Df\Payment\Operation\Source\Order::id()
 * @used-by \Df\Payment\Token::get()
 * @used-by \Df\StripeClone\Facade\Customer::cardIdForJustCreated()
 * @used-by \Df\Zoho\App::title()
 * @used-by \Dfe\FacebookLogin\Customer::longLivedAccessToken()
 * @used-by \Dfe\FacebookLogin\Customer::picture()
 * @used-by \Dfe\IPay88\Method::option()
 * @used-by \Dfe\Robokassa\Method::option()
 * @param string $v
 * @param int $sl [optional]
 * @throws DFE
 */
function df_result_sne($v, $sl = 0):string {$sl++;
	df_result_s($v, $sl);
	/**
	 * Раньше тут стояло `$method->assertParamIsString($v, $ord, $sl)`
	 * При второй попытке тут стояло `if (!$v)`, что тоже неправильно, ибо непустая строка '0' не проходит такую валидацию.
	 * 2022-11-10
	 * @see df_param_s()
	 * @see df_param_sne()
	 */
	return '' !== strval($v) ? $v : Q::raiseErrorResult(__FUNCTION__, [Q::NES], $sl);
}