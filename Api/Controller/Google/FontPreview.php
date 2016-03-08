<?php
namespace Df\Api\Controller\Google;
use Df\Api\Google\Font;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview;
// 2016-02-17
// Иначе bin/magento setup:di:compile почему-то выдаёт сбой:
// Cannot use Df\Api\Google\Fonts as Fonts because the name is already in use
// app/code/Df/Api/Controller/Google/FontPreview.php on line 6
use Df\Api\Google\Fonts as _Fonts;
class FontPreview extends \Df\Framework\App\Action\Image {
	/**
	 * 2015-11-29
	 * @override
	 * @see \Df\Framework\App\Action\Image::contents()
	 * @return string
	 */
	protected function contents() {return $this->preview()->contents();}

	/**
	 * 2015-11-29
	 * @override
	 * @see \Df\Framework\App\Action\Image::type()
	 * @return string
	 */
	protected function type() {return 'png';}

	/** @return string */
	private function family() {return df_first($this->familyA());}

	/** @return string[] */
	private function familyA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = explode(':', df_request('family'));
		}
		return $this->{__METHOD__};
	}

	/** @return Font */
	private function font() {return _Fonts::s()->get($this->family());}

	/** @return Preview */
	private function preview() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->variant()->preview();
		}
		return $this->{__METHOD__};
	}

	/** @return Variant */
	private function variant() {return $this->font()->variant($this->variantName());}

	/**
	 * 2015-11-29
	 * Например: "regular", "italic", "700", "700italic"
	 * @return string
	 */
	private function variantName() {return dfa($this->familyA(), 1, 'regular');}
}