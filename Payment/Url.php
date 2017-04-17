<?php
namespace Df\Payment;
use Df\Payment\Method as M;
/**
 * 2017-03-23
 * @see \Dfe\AllPay\Url
 * @see \Dfe\SecurePay\Url
 */
class Url {
	/**
	 * 2016-08-27
	 * @used-by dfp_url_api()
	 * @param string $url
	 * @param bool $test [optional]
	 * @param mixed[] ...$args [optional]
	 * @return string
	 */
	final function url($url, $test = null, ...$args) {return df_url_staged(
		!is_null($test) ? $test : $this->_m->test(), $url, $this->stageNames(), ...$args
	);}

	/**
	 * 2016-08-27
	 * Первый параметр — для test, второй — для live.
	 * @used-by url()
	 * @see \Dfe\AllPay\Url::stageNames()
	 * @see \Dfe\SecurePay\Url::stageNames()
	 * @return string[]
	 */
	protected function stageNames() {return $this->_stages;}

	/**
	 * 2017-03-23
	 * @used-by s()
	 * @param M $m
	 * @param string[] $stages
	 */
	private function __construct(M $m, array $stages) {$this->_m = $m; $this->_stages = $stages;}

	/**
	 * 2017-03-23
	 * @used-by __construct()
	 * @used-by url()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-03-23
	 * @used-by __construct()
	 * @used-by stageNames()
	 * @var string[]
	 */
	private $_stages;

	/**
	 * 2017-03-23
	 * @used-by dfp_url_api()
	 * @param string|object $m
	 * @param string[] $stages
	 * @return self
	 */
	final static function s($m, array $stages = []) {return dfcf(function(M $m, array $stages) {
		/** @var string $c */$c = df_con_hier($m, __CLASS__); return new $c($m, $stages ?: ['', '']);
	}, [dfpm($m), $stages]);}
}