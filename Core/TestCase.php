<?php
namespace Df\Core;
use Df\Config\Settings as S;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;
/**
 * 2016-11-10 Вынуждены использовать PHPUnit 4, потому что иначе будет сбой компиляции: https://mage2.pro/t/2260
 * @see \Df\Catalog\Test\product\attribute
 * @see \Df\Geo\Test\TestCase
 * @see \Df\Payment\TestCase
 * @see \Df\Zoho\Test\TestCase
 * @see \Dfe\BlackbaudNetCommunity\Test\Main
 * @see \Dfe\Dynamics365\Test\TestCase
 * @see \Dfe\Portal\Test\TestCase
 * @see \Dfe\Sift\Test\CaseT
 * @see \Inkifi\Core\T\CaseT
 */
abstract class TestCase extends TestCaseBase {
	/**
	 * 2017-02-26
	 * @see \Df\Payment\TestCase::s()
	 * @param object|string|null $m [optional]
	 */
    protected function s($m = null):S {return dfs($m ?: $this);}

	/**
	 * 2016-11-03
	 * @override
	 * @see \PHPUnit_Framework_TestCase::setUp()
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 * @see \Dfe\Omise\Test\TestCase::setUp()
	 * @see \Dfe\Stripe\Test\CaseT::setUp()
	 */
	protected function setUp():void {
		if (!self::$r) {
			self::$r = true;
			Bootstrap::create(BP, $_SERVER)->createApplication(Http::class);
			df_area_code_set_f();
		}
	}

	/**
	 * 2016-11-30
	 * https://3v4l.org/Ns95Q
	 * @var bool
	 */
	private static $r;
}

