<?php
namespace Df\Api\Google\Fonts;
use Df\Api\Google\Font\Variant\Preview\Params;
abstract class Png extends \Df\Core\O {
	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @param resource $image
	 * @return void
	 */
	abstract protected function draw($image);

	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @return int
	 */
	abstract protected function height();

	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @return int
	 */
	abstract protected function width();

	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Png::path()
	 * @return string[]
	 */
	abstract protected function pathRelativeA();

	/** @return string */
	public function contents() {
		if (!isset($this->{__METHOD__})) {
			$this->createIfNeeded();
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
				$this->createIfNeeded();
				$this->{__METHOD__} = df_media_url($this->path());
			}
			catch (\Exception $e) {
				df_log($e->getMessage());
				$this->{__METHOD__} = '';
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-30
	 * @return int|int[]
	 */
	protected function bgColor() {return $this->params()->bgColor();}

	/**
	 * @param resource $image
	 * @param int[] $rgba
	 * @return int
	 */
	protected function colorAllocateAlpha($image, array $rgba) {
		/** @var int|bool $result */
		$result = imagecolorallocatealpha($image, $rgba[0], $rgba[1], $rgba[2], df_a($rgba, 3, 0));
		df_assert(false !== $result);
		return $result;
	}

	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Png::createIfNeeded()
	 * @used-by \Df\Api\Google\Fonts\Sprite::datumPoints()
	 * @return void
	 */
	protected function create() {
		ob_start();
		try {
			$image = $this->image();
			try {
				imagepng($this->image());
			}
			finally {
				imagedestroy($image);
			}
			df_media_write($this->path(), ob_get_contents());
		}
		finally {
			ob_end_clean();
		}
	}

	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Png::contents()
	 * @used-by \Df\Api\Google\Fonts\Png::url()
	 * @used-by \Df\Api\Google\Fonts\Sprite::datumPoint()
	 * @return void
	 */
	protected function createIfNeeded() {
		if ($this->needToCreate()) {
			$this->create();
		}
	}

	/** @return Fs */
	protected function fs() {return Fs::s();}

	/**
	 * 2015-12-08
	 * Кэшировать результат нельзя!
	 * @used-by \Df\Api\Google\Fonts\Png::createIfNeeded()
	 * @see \Df\Api\Google\Fonts\Sprite::needToCreate()
	 * @return bool
	 */
	protected function needToCreate() {return !file_exists($this->path());}

	/** @return Params */
	protected function params() {return $this[self::$P__PARAMS];}

	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Png::create()
	 * @return resource
	 */
	private function image() {
		/** @var resource|bool $result */
		$result = imagecreatetruecolor($this->width(), $this->height());
		df_assert(false !== $result);
		$r = imagesavealpha($result, true);
		df_assert($r);
		$this->draw($result);
		return $result;
	}

	/** @return string */
	private function path() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->fs()->absolute($this->pathRelativeA());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PARAMS, Params::class);
	}
	/** @var string */
	protected static $P__PARAMS = 'params';
}