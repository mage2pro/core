<?php
use Zend_Http_Client as C;
/**
 * 2017-05-10
 * @used-by df_github_request()
 */
function df_github_token():string {return df_credentials('github');}

/**
 * 2017-05-10 https://developer.github.com/v3/repos/releases#get-the-latest-release
 * @used-by \Dfe\Portal\Test\Basic::t02()
 */
function df_github_repo_version(string $repo):string {return df_github_request("repos/$repo/releases/latest", 'tag_name');}

/**
 * 2017-05-10 https://developer.github.com/v3/repos/releases/#get-the-latest-release
 * @used-by df_github_repo_version()
 * @param string|null $k [optional]
 * @return string|null|array(string => mixed)
 */
function df_github_request(string $path, $k = null, $params = []) {
	$c = df_zf_http("https://api.github.com/$path")
		# 2017-06-28
		# «Difference between the Accept and Content-Type HTTP headers»
		# https://webmasters.stackexchange.com/questions/31212
		->setHeaders('accept', 'application/json')
		->setParameterGet(['access_token' => df_github_token()] + $params)
	; /** @var C $c */
	return dfa(df_json_decode($c->request()->getBody()), $k);
}
