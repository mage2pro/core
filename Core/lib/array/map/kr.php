<?php
use Df\Core\Exception as DFE;

/**
 * 2016-11-08 Функция принимает аргументы в любом порядке.
 * 2024-06-03
 * 1.1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 1.2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 1.3) https://php.net/manual/en/language.types.iterable.php
 * 2) We still can not use «Union Types» (e.g. `callable|iterable`) because they require PHP ≥ 8 (we need to support PHP ≥ 7.1):
 * 2.1) https://php.watch/versions/8.0/union-types
 * 2.2) https://3v4l.org/AOWmO
 * @used-by dfak_transform()
 * @used-by Df\Config\Source\Block::map()
 * @used-by Df\Core\Text\Regex::getErrorCodeMap()
 * @used-by Dfe\Color\Image::labels()
 * @used-by Dfe\Color\Plugin\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual::afterGetJsonConfig()
 * @used-by Dfe\Robokassa\Api\Options::p()
 * @used-by Doormall\Shipping\Partner\Entity::locations()
 * @param callable|iterable $a1
 * @param callable|iterable $a2
 * @return array(int|string => mixed)
 * @throws DFE
 */
function df_map_kr($a1, $a2):array {return df_map($a1, $a2, [], [], DF_BEFORE, true);}