<?php
namespace Df\Api\Controller\Google;
use Df\Api\Google\Font;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview;
use Df\Api\Google\Fonts as _Fonts;
class Fonts extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Json
	 */
	public function execute() {
		/*while ($this->busy()) {
			$this->wait();
		} */
		return df_controller_json(df_map(function(Font $font) {return [
			'family' => $font->family()
			, 'variants' => array_filter(array_map(function(Variant $variant) {
				/** @var string $url */
				$url = $variant->preview()->url();
				return !$url ? null : ['name' => $variant->name(), 'preview' => $url];
			}, $font->variants()))
		];}, _Fonts::s()));
	}
}
