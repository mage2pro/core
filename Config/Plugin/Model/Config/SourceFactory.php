<?php
namespace Df\Config\Plugin\Model\Config;
use Magento\Config\Model\Config\SourceFactory as Sb;
// 2015-11-14
final class SourceFactory {
	/**
	 * 2016-01-01
	 * Сюда мы попадаем при обработке ядром тега <source_model>.
	 * 2015-11-14
	 * Цель перекрытия — делаем наши источники данных независимыми друг от друга:
	 * ядро создаёт источники данных как объекты-одиночки:
	 * @see \Magento\Config\Model\Config\SourceFactory::create()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Model/Config/SourceFactory.php#L33
	 * а мы вместо этого создаём для каждого НАШЕГО поля отдельный источник данных.
	 * @see \Magento\Config\Model\Config\SourceFactory::create()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $c
	 * @return \Magento\Framework\Option\ArrayInterface|mixed
	 */
	function aroundCreate(Sb $sb, \Closure $f, $c) {return df_class_my($c) ? new $c : $f($c);}
}