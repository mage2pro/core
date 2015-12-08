<?php
namespace Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview\Params;
use Df\Api\Google\Fonts;
class Preview extends \Df\Api\Google\Fonts\Png {
	/**
	 * 2015-12-08
	 * Стандартный способ генерации идентификатора нас не устраивает,
	 * потому что он создаёт идентификатор случайным образом,
	 * а нам нужно, чтобы идентификатор был одним и тем же
	 * для двух любых запросов к серверу (чтобы сопоставлять preview и datumPoints).
	 * @override
	 * @see \Df\Core\O::getId()
	 * @used-by \Df\Api\Google\Fonts\Sprite::datumPoint()
	 * @used-by \Df\Api\Google\Fonts\Sprite::draw()
	 * @return string|int
	 */
	public function getId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(':', [$this->family(), $this->variant()->name()]);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\Api\Google\Fonts\Png::height()
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @used-by \Df\Api\Google\Fonts\Sprite::height()
	 * @used-by \Df\Api\Google\Fonts\Sprite::draw()
	 * @return int
	 */
	public function height() {return $this->params()->height();}

	/**
	 * 2015-12-08
	 * @return bool
	 */
	public function isAvailable() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->url();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\Api\Google\Fonts\Png::width()
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @used-by \Df\Api\Google\Fonts\Sprite::width()
	 * @used-by \Df\Api\Google\Fonts\Sprite::draw()
	 * @return int
	 */
	public function width() {return $this->params()->width();}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\Api\Google\Fonts\Png::draw()
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @param resource $image
	 * @return void
	 */
	protected function draw($image) {
		$r = imagefill($image, 0, 0, $this->colorAllocateAlpha($image, $this->bgColor()));
		df_assert($r);
		$r = imagettftext(
			$image
			, $this->fontSize()
			, 0
			, $this->marginLeft()
			, $this->coordYCentered()
			, $this->colorAllocateAlpha($image, $this->fontColor())
			, $this->ttfPath()
			, $this->family()
		);
		df_assert($r);
	}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\Api\Google\Fonts\Png::pathRelativeA()
	 * @used-by \Df\Api\Google\Fonts\Png::path()
	 * @return string[]
	 */
	protected function pathRelativeA() {
		return [
			'preview'
			, $this->folderFamily()
			, $this->fs()->nameResolution()
			, $this->fs()->namePng([$this->variant()->name(), $this->fs()->nameColorsSizeMargin()])
		];
	}

	/**
	 * 2015-11-30
	 * https://github.com/stylesplugin/styles-font-menu/blob/127946d9bb198357f39d3da47bf1908ce19844bd/classes/sfm-image-preview.php#L104-L124
	 * http://stackoverflow.com/a/15001168
	 * @return int
	 * @throws \Exception
	 */
	private function coordYCentered() {
		if (!isset($this->{__METHOD__})) {
			try {
				/** @var int[] $dims */
				$dims = imagettfbbox($this->fontSize(), 0, $this->ttfPath(), $this->family());
				df_assert(false !== $dims);
			}
			catch (\Exception $e) {
				throw new \Exception(
					"Unable to load the TTF file for the font"
					." «{$this->family()} ({$this->variant()->name()})»: «{$this->ttfPath()}»."
					."\n" . $e->getMessage()
					, 0, $e
				);
			}
			/** @var int $ascent */
			$ascent = abs($dims[7]);
			/** @var int $descent */
			$descent = abs($dims[1]);
			/** @var int $height */
			$height = $ascent + $descent;
			$image_height = $this->height();
			$this->{__METHOD__} = $ascent + $image_height / 2 - $height / 2;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function family() {return $this->variant()->font()->family();}

	/** @return string */
	private function folderFamily() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_fs_name($this->variant()->font()->family());;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-30
	 * @return int|int[]
	 */
	private function fontColor() {return $this->params()->fontColor();}

	/** @return int */
	private function fontSize() {return $this->params()->fontSize();}

	/** @return int */
	private function marginLeft() {return $this->params()->marginLeft();}

	/** @return string */
	private function ttfPath() {return $this->variant()->ttfPath();}

	/** @return Variant */
	private function variant() {return $this[self::$P__VARIANT];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__VARIANT, Variant::_C);
	}

	/**
	 * 2015-11-29
	 * @param Variant $variant
	 * @param Params $params
	 * @return Preview
	 */
	public static function i(Variant $variant, Params $params) {
		return new self([self::$P__VARIANT => $variant, self::$P__PARAMS => $params]);
	}
	/** @var string */
	private static $P__VARIANT = 'variant';
}
