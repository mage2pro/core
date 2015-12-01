<?php
namespace Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview\Params;
use Df\Api\Google\Fonts;
use Symfony\Component\Config\Definition\Exception\Exception;

class Preview extends \Df\Core\O {
	/** @return string */
	public function contents() {
		if (!isset($this->{__METHOD__})) {
			if (!$this->exists()) {
				$this->create();
			}
			$this->{__METHOD__} = file_get_contents($this->path());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-01
	 * Изначально реализация была «ленивой»:
			$this->exists()
			? df_media_url($this->path())
			: df_url_frontend('df-api/google/fontPreview', ['_query' => [
				'family' => implode(':', [$this->family(), $this->variant()->name()])
			] + $this->params()->getData()])
	 * Однако оказалось, что она крайне неэффективна:
	 * в клиентской части мы создаём много тегов IMG, и при добавлении в DOM
	 * браузер сразу делает кучу запросов к серверу по адресу src.
	 * Получается, что намного эффективнее сразу построить все картинки в едином запросе.
	 *
	 * Но df-api/google/fontPreview нам всё равно пригодится для динамических запросов!
	 *
	 * @return string
	 */
	public function url() {
		if (!isset($this->{__METHOD__})) {
			try {
				if (!$this->exists()) {
					$this->create();
				}
				$this->{__METHOD__} = df_media_url($this->path());
			}
			catch (\Exception $e) {
				df_log($e->getMessage());
				$this->{__METHOD__} = '';
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function baseName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_fs_name(implode('_', [
				$this->variant()->name()
				, 's' . df_pad0(2, $this->fontSize())
				, 'f' . implode('-', $this->fontColor())
				, 'b' . implode('-', $this->bgColor())
				, 'm' . $this->marginLeft()
			])) . '.png';
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-30
	 * @return int|int[]
	 */
	private function bgColor() {return $this->params()->bgColor();}

	/**
	 * @param resource $image
	 * @param int[] $rgba
	 * @return int
	 */
	private function colorAllocateAlpha($image, array $rgba) {
		/** @var int|bool $result */
		$result = imagecolorallocatealpha($image, $rgba[0], $rgba[1], $rgba[2], df_a($rgba, 3, 0));
		df_assert(false !== $result);
		return $result;
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

	/**
	 * @used-by \Df\Api\Google\Font\Variant\Preview::contents()
	 * @return void
	 */
	private function create() {
		/** @var resource $image */
		$image = $this->imagecreatetruecolor();
		$r = imagesavealpha($image, true);
		df_assert($r);
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
		ob_start();
		imagepng($image);
		df_media_write($this->path(), ob_get_clean());
	}

	/** @return bool */
	private function exists() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = file_exists($this->path());
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

	/** @return string */
	private function folderResolution() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2015-11-29
			 * http://stackoverflow.com/a/6070420
			 * @param int|string $size
			 * @return string
			 */
			$pad = function($size) {return df_pad0(4, $size);};
			$this->{__METHOD__} = implode('x', [$pad($this->width()), $pad($this->height())]);
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
	private function height() {return $this->params()->height();}

	/** @return resource */
	private function imagecreatetruecolor() {
		/** @var int|bool $result */
		$result = imagecreatetruecolor($this->width(), $this->height());
		df_assert(false !== $result);
		return $result;
	}

	/** @return int */
	private function marginLeft() {return $this->params()->marginLeft();}

	/** @return Params */
	private function params() {return $this[self::$P__PARAMS];}

	/** @return string */
	private function path() {
		return Fonts::basePathAbsolute() . $this->pathRelative();
	}

	/** @return string */
	private function pathRelative() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_concat_path(
				'preview'
				, $this->folderFamily()
				, $this->folderResolution()
				, $this->baseName()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function ttfPath() {return $this->variant()->ttfPath();}

	/** @return Variant */
	private function variant() {return $this[self::$P__VARIANT];}

	/** @return int */
	private function width() {return $this->params()->width();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__PARAMS, Params::_C)
			->_prop(self::$P__VARIANT, Variant::_C)
		;
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
	private static $P__PARAMS = 'params';
	/** @var string */
	private static $P__VARIANT = 'variant';
}
