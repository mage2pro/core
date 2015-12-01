<?php
namespace Df\Api\Google\Font\Variant\Preview;
class Params  extends \Df\Core\O {
	/** @return int[] */
	public function bgColor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->rgb($this->bgColorRaw());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function bgColorRaw() {return $this[self::$P__BG_COLOR];}

	/** @return int[] */
	public function fontColor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->rgb($this->fontColorRaw());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function fontColorRaw() {return $this[self::$P__FONT_COLOR];}

	/** @return int */
	public function fontSize() {return $this[self::$P__FONT_SIZE];}

	/**
	 * 2015-12-01
	 * @override
	 * @see \Df\Core\O::getId()
	 * @return string
	 */
	public function getId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			df_result_string($result);
			$this->{__METHOD__} = implode('-', [
				$this->width(), $this->height(), $this->fontSize()
				, implode('-', $this->fontColor()), implode('-', $this->bgColor())
			]);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function height() {return $this[self::$P__HEIGHT];}

	/** @return int */
	public function marginLeft() {return $this[self::$P__MARGIN_LEFT];}

	/** @return int */
	public function width() {return $this[self::$P__WIDTH];}

	/**
	 * @param $colorS
	 * @return int[]
	 */
	private function rgb($colorS) {return df_int(explode('|', $colorS));}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__BG_COLOR, RM_V_STRING_NE)
			->_prop(self::$P__FONT_COLOR, RM_V_STRING_NE)
			->_prop(self::$P__FONT_SIZE, RM_V_NAT)
			->_prop(self::$P__HEIGHT, RM_V_NAT)
			->_prop(self::$P__MARGIN_LEFT, RM_V_NAT0)
			->_prop(self::$P__WIDTH, RM_V_NAT)
		;
	}

	/** @return Params */
	public static function fromRequest() {
		/** @var array(string => string|int) $params */
		$params = [
			self::$P__WIDTH => 400
			,self::$P__HEIGHT => 50
			,self::$P__FONT_SIZE => 14
			,self::$P__FONT_COLOR => '0|0|0|0'
			,self::$P__BG_COLOR => '255|255|255|127'
			,self::$P__MARGIN_LEFT => 0
		];
		static $r;return $r ? $r : $r = new self(
			df_select_a(df_request() + $params, array_keys($params))
		);
	}

	/** @used-by \Df\Api\Google\Font\Variant\Preview::_construct */
	const _C = __CLASS__;

	/** @var string */
	private static $P__BG_COLOR = 'bgColor';
	/** @var string */
	private static $P__FONT_COLOR = 'fontColor';
	/** @var string */
	private static $P__FONT_SIZE = 'fontSize';
	/** @var string */
	private static $P__HEIGHT = 'height';
	/** @var string */
	private static $P__MARGIN_LEFT = 'marginLeft';
	/** @var string */
	private static $P__WIDTH = 'width';
}