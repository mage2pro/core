<?php
namespace Df\Payment;
use Df\Payment\Method as M;
/**
 * 2017-03-23
 * @see \Dfe\AllPay\Url
 * @see \Dfe\PostFinance\Url
 * @see \Dfe\SecurePay\Url
 * @see \Dfe\YandexKassa\Url
 */
class Url {
	/**
	 * 2016-08-27
	 * @used-by dfp_url_api()
	 * @param mixed ...$a [optional]
	 */
	final function url(string $url, bool $test = null, ...$a):string {return df_url_staged(
		!is_null($test) ? $test : $this->_m->test(), $url, $this->stageNames(), ...$a
	);}

	/**
	 * 2016-08-27 The method returns a 2-tuple: the first element is for the test mode, the second is for the production mode.
	 * @used-by self::url()
	 * @see \Dfe\AllPay\Url::stageNames()
	 * @see \Dfe\PostFinance\Url::stageNames()
	 * @see \Dfe\SecurePay\Url::stageNames()
	 * @see \Dfe\YandexKassa\Url::stageNames()
	 * @return string[]
	 */
	protected function stageNames():array {return $this->_stages;}

	/**
	 * 2017-03-23
	 * @used-by self::s()
	 * @param string[] $stages
	 */
	private function __construct(M $m, array $stages) {$this->_m = $m; $this->_stages = $stages;}

	/**
	 * 2017-03-23
	 * @used-by self::__construct()
	 * @used-by self::url()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-03-23
	 * @used-by self::__construct()
	 * @used-by self::stageNames()
	 * @var string[]
	 */
	private $_stages;

	/**
	 * 2017-03-23
	 * @used-by dfp_url_api()
	 * @param string|object $m
	 * @param string[] $stages
	 */
	final static function f($m, array $stages = []):self {return dfcf(function(M $m, array $stages) {
		/** @var string $c */$c = df_con_hier($m, __CLASS__); return new $c($m, $stages ?: ['', '']);
	}, [dfpm($m), $stages]);}
}