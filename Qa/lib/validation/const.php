<?php
if (!defined ('PHP_INT_MIN')) {
	define('PHP_INT_MIN', ~PHP_INT_MAX);
}
/**
 * 2016-09-02
 * К сожалению, конструкции типа
 * const DF_F_TRIM = \Df\Zf\Filter\StringT\Trim::class;
 * приводят к сбою дибильного компилятора Magento 2:
 * https://github.com/magento/magento2/issues/6179
 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/25
 */
const DF_F_TRIM = '\Df\Zf\Filter\StringT\Trim';
const DF_V_ARRAY = '\Df\Zf\Validate\ArrayT';
const DF_V_BOOL = '\Df\Zf\Validate\Boolean';
const DF_V_FLOAT = '\Df\Zf\Validate\FloatT';
const DF_V_INT = '\Df\Zf\Validate\IntT';
# 2-буквенный код страны по стандарту ISO 3166-1 alpha-2.
# https://ru.wikipedia.org/wiki/ISO_3166-1
const DF_V_ISO2 = '\Df\Zf\Validate\StringT\Iso2';
const DF_V_STRING = '\Df\Zf\Validate\StringT';
const DF_V_STRING_NE = '\Df\Zf\Validate\StringT\NotEmpty';