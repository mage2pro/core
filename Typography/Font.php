<?php
namespace Df\Typography;
class Font extends \Df\Core\O {
	/**
	 * 2015-12-17
	 * Цвет текста.
	 * Для задания цвета мы используем элемент управления Spectrum,
	 * и код цвета у нас хранится либо в формате rgb, либо в формате rgba,
	 * например: «rgba(0, 0, 255, 0.78)».
	 * http://code.dmitry-fedyuk.com/m2/all/blob/5b2bc5846401666915eabb0dd10fc68b226e7e57/Framework/View/adminhtml/web/formElement/color/main.js#L50
	 * Современные браузеры прекрасно понимают нотацию rgba:
	 * http://stackoverflow.com/a/10835846
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/opacity
	 * @return string
	 */
	public function color() {return $this[__FUNCTION__];}

	/** @return bool */
	public function enabled() {return $this->b('setup');}

	/** @return string */
	public function family() {return df_first($this->familyA());}

	/** @return bool */
	public function letter_case() {return $this[__FUNCTION__];}

	/** @return Size */
	public function letter_spacing() {return $this->_size(__FUNCTION__);}

	/**
	 * 2015-12-16
	 * @return string
	 */
	public function link() {
		if (!isset($this->{__METHOD__})) {
			/** http://stackoverflow.com/questions/4659345 */
			$this->{__METHOD__} =
				'default' === $this->familyS()
				? ''
				: '//fonts.googleapis.com/css?family=' . urlencode($this->family())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function needScale() {
		return
			100 !== $this->scale_horizontal()->valueI()
			|| 100 !== $this->scale_vertical()->valueI()
		;
	}

	/** @return Size */
	public function scale_horizontal() {return $this->_size(__FUNCTION__);}

	/** @return Size */
	public function scale_vertical() {return $this->_size(__FUNCTION__);}

	/**
	 * 2015-12-16
	 * @return string
	 */
	public function scaleRule() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf('scale(%.2f,%.2f)'
				, $this->scale_horizontal()->valueF() / 100
				, $this->scale_vertical()->valueF() / 100
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Size */
	public function size() {return $this->_size(__FUNCTION__);}

	/**
	 * 2015-12-16
	 * @return string
	 */
	public function style() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a(
				['regular' => 'normal', 'italic' => 'italic']
				, $this->variantWord()
				, $this->italic() ? 'italic' : ''
			);
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function underline() {return $this->b(__FUNCTION__);}

	/**
	 * 2015-12-16
	 * @return string
	 */
	public function weight() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->variantNumber() ?: ($this->bold() ? 'bold' : 'normal');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @return Size
	 */
	private function _size($key) {
		df_param_string_not_empty($key, 0);
		if (!isset($this->{__METHOD__}[$key])) {
			$this->{__METHOD__}[$key] = new Size($this[$key]);
		}
		return $this->{__METHOD__}[$key];
	}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @return bool
	 */
	private function b($key) {return isset($this->_data[$key]);}

	/** @return bool */
	private function bold() {return $this->b(__FUNCTION__);}

	/**
	 * 2015-12-16
	 * @return string[]
	 */
	private function familyA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = explode(':', $this->familyS());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function familyS() {return $this['family'];}

	/** @return bool */
	private function italic() {return $this->b(__FUNCTION__);}

	/**
	 * 2015-12-16
	 * @return string
	 */
	private function variant() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->familyA(), 1, '');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * Вычленяет «700» из «700italic»
	 * @return string
	 */
	private function variantNumber() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_nts(df_preg_match_int('#\d+#', $this->variant(), false));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * Вычленяет «italic» из «italic»
	 * @return string
	 */
	private function variantWord() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = str_replace($this->variantNumber(), '', $this->variant());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-16
	 * @param string $selector
	 * @return string
	 */
	public function css($selector) {return Css::r($this, $selector);}
}