<?php
use Exception as E;
use Magento\Framework\DataObject;

/**
 * 2016-12-22
 * @param DataObject|mixed[]|mixed|E $v
 * @param array(string => mixed) $context [optional]
 */
function df_sentry($v, array $context = []) {
	if (true || !df_my_local()) {
		$context = df_extend(['extra' => [
			'Magento version' => df_magento_version()
		]], $context);
		if ($v instanceof E) {
			// 2016-12-22
			// https://docs.sentry.io/clients/php/usage/#reporting-exceptions
			df_sentry_m()->captureException($v, $context);
		}
		else {
			$v = df_dump($v);
			// 2016-12-22
			// https://docs.sentry.io/clients/php/usage/#reporting-other-errors
			df_sentry_m()->captureMessage($v, [], $context);
		}
	}
}

/**
 * 2016-12-22
 * @return \Raven_Client
 */
function df_sentry_m() {return dfcf(function() {new \Raven_Client(
	'https://0574710717d5422abd1c5609012698cd:32ddadc0944c4c1692adbe812776035f@sentry.io/124181'
);});}