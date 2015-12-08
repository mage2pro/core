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
