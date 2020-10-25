<?php
/**
 * 2020-10-25
 * @used-by \Dfe\Color\Image::__construct()
 * @used-by \TFC\Image\Command::p()
 */
function df_google_init_service_account() {dfcf(function() {putenv(
	// 2019-08-21
	// https://googleapis.github.io/google-cloud-php/#/docs/google-cloud/v0.107.1/guides/authentication
	// https://github.com/googleapis/google-auth-library-php/tree/v1.5.2#application-default-credentials
	'GOOGLE_APPLICATION_CREDENTIALS=' . df_fs_etc('google-app-credentials.json')
);});}