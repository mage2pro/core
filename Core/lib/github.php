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
 * 2023-03-02 https://docs.github.com/en/rest/releases/releases?apiVersion=2022-11-28#get-the-latest-release
 * @used-by df_github_repo_version()
 * @return string|null|array(string => mixed)
 */
function df_github_request(string $path, string $k = '', $params = []) {
	$c = df_zf_http("https://api.github.com/$path")
		# 2017-06-28
		# «Difference between the Accept and Content-Type HTTP headers»
		# https://webmasters.stackexchange.com/questions/31212
		->setHeaders([
			'accept' => 'application/json'
			# 2023-03-02
			# 1) https://developer.github.com/changes/2020-02-10-deprecating-auth-through-query-param/#changes-to-make
			# 2) https://docs.github.com/en/rest/releases/releases?apiVersion=2022-11-28#get-the-latest-release
			,'authorization' => 'token ' . df_github_token()
			# 2023-03-02
			# It is not required but nice to have.
			# https://docs.github.com/en/rest/releases/releases?apiVersion=2022-11-28#get-the-latest-release
			,'X-GitHub-Api-Version' => '2022-11-28'
		])
		->setParameterGet($params)
	; /** @var C $c */
	return dfa(df_json_decode($c->request()->getBody()), $k);
}
