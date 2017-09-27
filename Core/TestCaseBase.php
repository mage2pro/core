<?php
namespace Df\Core;
/**
 * 2017-09-13
 * Magento 2 versions < 2.2.0-RC1.8 use PHPUnit 4.
 * Magento 2 versions >= 2.2.0-RC1.8 use PHPUnit 6.2.0.
 * `«Fatal error: Class \'PHPUnit\Framework\TestCase\' not found in /app/vendor/mage2pro/core/Core/TestCase.php on line 6» on compilation`: https://mage2.pro/t/2260
 * `PHPUnit has been upgraded from 4.1.0 to 6.2.x in Magento 2.2.0-RC1.8`: https://mage2.pro/t/4275
 */
// @codingStandardsIgnoreFile
if (@class_exists('\PHPUnit\Framework\TestCase')) {
	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	abstract class TestCaseBase extends \PHPUnit\Framework\TestCase {}
}
else {
	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	abstract class TestCaseBase extends \PHPUnit_Framework_TestCase {
		/**
		 * 2016-11-10
		 * Метод @see \PHPUnit\Framework\TestCase::expectException() появился только в PHPUnit 5.2.0,
		 * поэтому эмулируем его.
		 * @param string $exception
		 */
		function expectException($exception) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->setExpectedException($exception);}
	}
}