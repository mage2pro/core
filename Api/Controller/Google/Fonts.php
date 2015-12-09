<?php
namespace Df\Api\Controller\Google;
use Df\Api\Google\Font;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview;
use Df\Api\Google\Font\Variant\Preview\Params;
use Df\Api\Google\Fonts as _Fonts;
use Df\Api\Google\Fonts\Sprite;
use Df\Core\Cache;
class Fonts extends \Magento\Framework\App\Action\Action {
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
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Json
	 */
	public function execute() {
		df_response_cache_max();
		$this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
		return df_sync($this, function() {return df_controller_json(
			Cache::i(null, 30 * 86400)->p(function() {return df_json_encode([
				'sprite' => $this->sprite()->url()
				,'fonts' => array_filter(df_map(function(Font $font) {
					return array_filter(array_map(function(Variant $variant) {
						return $this->sprite()->datumPoint($variant->preview());
					}, $font->variants()));
				}, _Fonts::s()))
			]);}, __METHOD__, df_request(), null)
		);});
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
