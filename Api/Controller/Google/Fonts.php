<?php
namespace Df\Api\Controller\Google;
use Df\Api\Google\Font;
use Df\Api\Google\Fonts as _Fonts;
class Fonts extends \Magento\Framework\App\Action\Action {
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Json
	 */
	public function execute() {
		return df_controller_json()->setData(
			df_map(function(Font $font) {
				return [
					'family' => $font->family()
					,'variants' => $font->variants()
				];
			}, _Fonts::s())
		);
	}
}
