<?php

/**
 * 2020-02-02
 * It returns a map:
 * 	{
 *		"authorizenet_acceptjs": "Credit Card (Authorize.Net)",
 *		"authorizenet_directpost": "Authorize.net",
 *		<...>
 *	}
 * @see dfp_methods_o()
 * @used-by dfp_methods_o()
 * @return array(array(string => string))
 */
function dfp_methods():array {return dfcf(function() {return df_map_k(function($k, $v) {return
	'Credit Card' !== $v ? $v : df_desc($v, $k)
;}, dfp_h()->getPaymentMethodList(true));});}

/**
 * 2020-02-02
 * It returns an options list:
 * 	[
 *		{"label": "Credit Card (Authorize.Net)", "value": "authorizenet_acceptjs"},
 *		{"label": "Authorize.net", "value": "authorizenet_directpost"},
 * 		<...>
 *	]
 * @see dfp_methods()
 * @used-by Dfe\Sift\PM\FE::onFormInitialized()
 * @return array(array(string => string))
 */
function dfp_methods_o():array {return df_map_to_options(dfp_methods());}