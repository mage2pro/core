<?php
namespace Df\Api\Controller\Google;
use Df\Api\Google\Font;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview;
use Df\Api\Google\Fonts as _Fonts;
class Fonts extends \Magento\Framework\App\Action\Action {
	/**
	 * 2015-12-07
	 * @todo По-хорошему, можно оптимизировать обработку так,
	 * чтобы картинки возвращались одним общим спрайтом.
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Json
	 */
	public function execute() {
		return df_sync($this, function() {
			return df_controller_json(df_map(function(Font $font) {return [
				'family' => $font->family()
				, 'variants' => array_filter(array_map(function(Variant $variant) {
					/** @var string $url */
					$url = $variant->preview()->url();
					return !$url ? null : ['name' => $variant->name(), 'preview' => $url];
				}, $font->variants()))
			];}, _Fonts::s()));
		});
	}
}
