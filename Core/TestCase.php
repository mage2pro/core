<?php
// 2016-11-03
namespace Df\Core;
use Df\Config\Settings as S;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;
/**
 * 2016-11-10
 * Вынуждены использовать PHPUnit 4, потому что иначе будет сбой компиляции: https://mage2.pro/t/2260
 * @see \Df\Payment\TestCase
 * @see \Dfe\Dynamics365\T\TestCase
 * @see \Dfe\Portal\T\TestCase
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase {
    /**
     * 2016-11-10
	 * Метод @see \PHPUnit\Framework\TestCase::expectException() появился только в PHPUnit 5.2.0,
	 * поэтому эмулируем его.
	 * @param string $exception
     */
    function expectException($exception) {$this->setExpectedException($exception);}

	/**
	 * 2017-02-26
	 * @see \Df\Payment\TestCase::s()
	 * @param object|string|null $m [optional]
	 * @return S
	 */
    protected function s($m = null) {return dfc($this, function($m) {return
		S::convention($m ?: $this)
	;}, [$m]);}

	/**
	 * 2016-11-03
	 * @override
	 * @see \PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		if (!self::$r) {
			self::$r = true;
			Bootstrap::create(BP, $_SERVER)->createApplication(Http::class);
			df_app_state()->setAreaCode('frontend');
		}
	}

	/**
	 * 2016-11-30
	 * https://3v4l.org/Ns95Q
	 * @var bool
	 */
	private static $r;
}

