<?php
/**
 * 2020-09-25
 * @used-by df_log_l()
 * @used-by \Df\Qa\Failure\Error::preface()
 * @return array(string => mixed)
 */
function df_context():array {return [
	['mage2pro/core' => df_core_version(), 'Magento' => df_magento_version(), 'PHP' => phpversion()]
	+ (df_is_cli()
		? ['Command' => df_cli_cmd()]
		: ([
			# 2021-04-18 "Include the visitor's IP address to Mage2.PRO reports": https://github.com/mage2pro/core/issues/151
			'IP Address' => df_visitor_ip()
			,'Referer' => df_referer()
			# 2021-06-05 "Log the request method": https://github.com/mage2pro/core/issues/154
			,'Request Method' => df_request_method()
			,'URL' => df_current_url()
			# 2021-04-18 "Include the visitor's `User-Agent` to Mage2.PRO reports":
			# https://github.com/mage2pro/core/issues/152
			,'User-Agent' => df_request_ua()
		] + (!df_request_o()->isPost() ? [] : ['Post' => $_POST]))
	)
];}