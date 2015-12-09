<?php
namespace Df\Api\Controller\Google;
use Df\Api\Google\Font;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview;
use Df\Api\Google\Font\Variant\Preview\Params;
use Df\Api\Google\Fonts as _Fonts;
use Df\Api\Google\Fonts\Sprite;
class Fonts extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Json
	 */
	public function execute() {
		/**
		 * 2015-12-09
		 * На странице может быть сразу несколько данных элементов управления.
		 * Возникает проблема: как синхронизировать их одновременные обращения к серверу за данными?
		 * Проблемой это является, потому что генерация образцов шрифтов —
		 * длительная (порой минуты) задача со множеством файловых операций.
		 * Параллельный запуск сразу двух таких генераций
		 * (а они будут выполняться разными процессами PHP)
		 * почти наверняка приведёт к файловым конфликтам и ошибкам,
		 * да и вообще смысла в этом никакого нет:
		 * зачем параллельно делать одно и то же с одними и теми же объектами?
		 * Эта проблема была решена в серверной части применением функции @uses df_sync
		 */
		return df_sync($this, function() {
			return df_controller_json([
				'sprite' => $this->sprite()->url()
				,'fonts' => array_filter(df_map(function(Font $font) {
					/** @var array(string => mixed) $variants */
					$variants = array_filter(array_map(function(Variant $variant) {
						/** @var array(string => int)|null $datumPoint  */
						$datumPoint = $this->sprite()->datumPoint($variant->preview());
						return !$datumPoint ? null : [
							'name' => $variant->name()
							, 'datumPoint' => array_combine(['x', 'y'], $datumPoint)
						];
					}, $font->variants()));
					return !$variants ? null : [
						'family' => $font->family()
						,'variants' => $variants
					];
				}, _Fonts::s()))
			]);
		});
	}

	/**
	 * 2015-12-08
	 * @return Sprite
	 */
	private function sprite() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Sprite::i(_Fonts::s(), Params::fromRequest());
		}
		return $this->{__METHOD__};
	}
}
