<?php
/**
 * 2020-10-25
 * 2021-11-22
 * 1) «A service account is a special kind of account used by an application or compute workload,
 * such as a Compute Engine virtual machine (VM) instance, rather than a person.
 * Applications use service accounts to make authorized API calls.»
 * https://cloud.google.com/iam/docs/service-accounts#what_are_service_accounts
 * 2) «Service accounts are special Google accounts
 * that can be used by applications to access Google APIs programmatically via OAuth 2.0.
 * A service account uses an OAuth 2.0 flow that does not require human authorization.
 * Instead, it uses a key file that only your application can access.»
 * https://developers.google.com/shopping-content/guides/how-tos/service-accounts
 * @used-by Dfe\Color\Image::__construct()
 * @used-by TFC\Image\Command\C1::p()
 * @used-by TFC\Image\Command\C2::p()
 * @used-by TFC\Image\Command\C3::p()
 */
function df_google_init_service_account():void {dfcf(function() {putenv(
	# 2019-08-21
	# https://googleapis.github.io/google-cloud-php/#/docs/google-cloud/v0.107.1/guides/authentication
	# https://github.com/googleapis/google-auth-library-php/tree/v1.5.2#application-default-credentials
	# 2021-11-22
	# 1) https://github.com/googleapis/google-api-php-client/blob/v2.11.0/README.md#authentication-with-service-accounts
	# 2) https://github.com/googleapis/google-auth-library-php/tree/v1.18.0#application-default-credentials
	'GOOGLE_APPLICATION_CREDENTIALS=' . df_fs_etc('google-app-credentials.json')
);});}