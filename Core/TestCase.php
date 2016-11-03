<?php
// 2016-11-03
namespace Df\Core;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;
abstract class TestCase extends \PHPUnit\Framework\TestCase {
	/**
	 * 2016-11-03
	 * @override
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 * @return void
	 */
	protected function setUp() {Bootstrap::create(BP, $_SERVER)->createApplication(Http::class);}
}

