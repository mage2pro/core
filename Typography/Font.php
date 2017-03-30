<?php
namespace Df\Typography;
use Df\Config\Source\LetterCase;
use Df\Core\Exception as DFE;
final class Font extends \Df\Config\O {
	/**
	 * 2016-08-03
	 * @override
	 * @see \Df\Config\O::validate()
	 * @used-by \Df\Config\Backend\Serialized::validate()
	 * @throws DFE
	 */
	function validate() {df_assert(!is_array($this['scale_horizontal']));}

	/**
	 * 2015-12-17
	 * Цвет текста.
	 * Для задания цвета мы используем элемент управления Spectrum,
	 * и код цвета у нас хранится либо в формате rgb, либо в формате rgba,
	 * например: «rgba(0, 0, 255, 0.78)».
	 * https://github.com/mage2pro/core/tree/5b2bc5846401666915eabb0dd10fc68b226e7e57/Framework/View/adminhtml/web/formElement/color/main.js#L50
	 * Современные браузеры прекрасно понимают нотацию rgba:
	 * http://stackoverflow.com/a/10835846
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/opacity
	 * @return string
	 */
	function color() {return $this->v();}

	/** @return bool */
	function enabled() {return $this->b();}

	/** @return string */
	function family() {return df_first($this->familyA());}

	/**
	 * 2015-12-25
	 * @return bool
	 */
	function familyIsStandard() {return 'default' === $this->familyS();}

	/** @return bool */
	function letter_case() {return $this->v();}

	/** @return Size */
	function letter_spacing() {return $this->_size();}

	/**
	 * 2015-12-16
	 * http://stackoverflow.com/questions/4659345
	 * @return string
	 */
	function link() {return dfc($this, function() {return $this->familyIsStandard() ? '' :
		'//fonts.googleapis.com/css?family=' . urlencode($this->family())
	;});}

	/** @return bool */
	function needScale() {return
		100 !== intval($this->scale_horizontal())
		|| 100 !== intval($this->scale_vertical())
	;}

	/** @return float */
	function scale_horizontal() {return $this->f();}

	/** @return float */
	function scale_vertical() {return $this->f();}

	/**
	 * 2015-12-16
	 * @return string
	 */
	function scaleRule() {return dfc($this, function() {return
		sprintf('scale(%.2f,%.2f)'
			, $this->scale_horizontal() / 100
			, $this->scale_vertical() / 100
		)
	;});}

	/** @return Size */
	function size() {return $this->_size();}

	/**
	 * 2015-12-16
	 * @return string
	 */
	function style() {return dfc($this, function() {return dfa(
		['regular' => 'normal', 'italic' => 'italic']
		, $this->variantWord()
		, $this->italic() ? 'italic' : ''
	);});}

	/** @return bool */
	function underline() {return $this->b();}

	/**
	 * 2015-12-16
	 * @return string
	 */
	function weight() {return dfc($this, function() {return
		$this->variantNumber() ?: ($this->bold() ? 'bold' : 'normal')
	;});}

	/**
	 * 2015-12-16
	 * @return Size
	 */
	private function _size() {return dfc($this, function($key) {return
		new Size($this[$key])
	;}, [df_caller_f()]);}

	/** @return bool */
	private function bold() {return $this->b();}

	/**
	 * 2015-12-16
	 * @return string[]
	 */
	private function familyA() {return dfc($this, function() {return
		explode(':', $this->familyS())
	;});}

	/** @return string */
	private function familyS() {return $this[self::family];}

	/** @return bool */
	private function italic() {return $this->b();}

	/**
	 * 2015-12-16
	 * @return string
	 */
	private function variant() {return dfc($this, function() {return
		dfa($this->familyA(), 1, '')
	;});}

	/**
	 * 2015-12-16
	 * Вычленяет «700» из «700italic»
	 * @return string
	 */
	private function variantNumber() {return dfc($this, function() {return
		df_nts(df_preg_match_int('#\d+#', $this->variant(), false))
	;});}

	/**
	 * 2015-12-16
	 * Вычленяет «italic» из «italic»
	 * @return string
	 */
	private function variantWord() {return dfc($this, function() {return
		str_replace($this->variantNumber(), '', $this->variant())
	;});}

	/**
	 * 2015-12-16
	 * @param string $selector
	 * @return string
	 */
	function css($selector) {
		/** @var string $result */
		if (!$this->enabled()) {
			$result = '';
		}
		else {
			/** @var Css $css */
			$css = Css::i($selector);
			$css->rule('font-weight', $this->weight());
			$css->rule('color', $this->color());
			$css->rule('font-style', $this->style());
			if ($this->underline()) {
				$css->rule('text-decoration', 'underline');
			}
			switch ($this->letter_case()) {
				case LetterCase::$LOWERCASE:
					$css->rule('text-transform', 'lowercase');
					break;
				case LetterCase::$UCFIRST:
					/**
					 * 2015-11-14
					 * .link { text-transform: lowercase; }
					 * .link:first-letter {text-transform: uppercase;}
					 * http://stackoverflow.com/a/10256138
					 */
					$css->rule('text-transform', 'lowercase');
					$css->rule('text-transform', 'uppercase', ':first-letter');
					break;
				case LetterCase::$UCWORDS:
					$css->rule('text-transform', 'capitalize');
					break;
				case LetterCase::$UPPERCASE:
					$css->rule('text-transform', 'uppercase');
					break;
			}
			if ($this->letter_spacing()->value()) {
				$css->rule('letter-spacing', $this->letter_spacing());
			}
			if ($this->needScale()) {
				/** @var string[] $names */
				$names = [
					'transform'
					, '-webkit-transform'
					, '-moz-transform'
					, '-ms-transform'
					, '-o-transform'
				];
				foreach ($names as $name) {
					/** @var string $name */
					$css->rule($name, $this->scaleRule());
				}
			}
			if ($this->size()->value()) {
				$css->rule('font-size', $this->size());
			}
			if ('default' !== $this->family()) {
				$css->rule('font-family', df_quote_single($this->family()));
			}
			$result = $css->render();
		}
		return $result;
	}

	const bold = 'bold';
	const color = 'color';
	const enabled = 'enabled';
	const family = 'family';
	const italic = 'italic';
	const letter_case = 'letter_case';
	const letter_spacing = 'letter_spacing';
	const scale_horizontal = 'scale_horizontal';
	const scale_vertical = 'scale_vertical';
	const size = 'size';
	const underline = 'underline';
}