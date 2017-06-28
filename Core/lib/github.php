<?php
use Zend_Http_Client as C;
/**
 * 2017-05-10
 * @return string
 */
function df_github_token() {return df_credentials('github');}

/**
 * 2017-05-10
 * https://developer.github.com/v3/repos/releases/#get-the-latest-release
 * @used-by df_github_request()
 * @param string $repo
 * @return string
 */
function df_github_repo_version($repo) {return df_github_request("repos/$repo/releases/latest", 'tag_name');}

/**
 * 2017-05-10
 * https://developer.github.com/v3/repos/releases/#get-the-latest-release
 * @param string $path
 * @param string|null $k [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_github_request($path, $k = null, $params = []) {
	/** @var C $c */
	$c = (new C)
		->setConfig(['timeout' => 120])
		// 2017-06-28
		// «Difference between the Accept and Content-Type HTTP headers»
		// https://webmasters.stackexchange.com/questions/31212
		->setHeaders('accept', 'application/json')
		->setMethod(C::GET)
		->setParameterGet(['access_token' => df_github_token()] + $params)
		->setUri("https://api.github.com/$path")
	;
	return dfak(df_json_decode($c->request()->getBody()), $k);
}
