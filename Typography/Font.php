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
	function validate():void {df_assert(!is_array($this['scale_horizontal']));}

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
	 */
	function color():string {return $this->v();}

	/**
	 * 2015-12-16
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
	 */
	function css(string $selector):string {/** @var string $result */
		if (!$this->enabled()) {
			$result = '';
		}
		else {
			$css = Css::i($selector); /** @var Css $css */
			$css->rule('font-weight', $this->weight());
			$css->rule('color', $this->color());
			$css->rule('font-style', $this->style());
			if ($this->underline()) {
				$css->rule('text-decoration', 'underline');
			}
			switch ($this->letter_case()) {
				case LetterCase::LOWERCASE:
					$css->rule('text-transform', 'lowercase');
					break;
				case LetterCase::UCFIRST:
					# 2015-11-14
					#		.link { text-transform: lowercase; }
					#		.link:first-letter {text-transform: uppercase;}
					# http://stackoverflow.com/a/10256138
					$css->rule('text-transform', 'lowercase');
					$css->rule('text-transform', 'uppercase', ':first-letter');
					break;
				case LetterCase::UCWORDS:
					$css->rule('text-transform', 'capitalize');
					break;
				case LetterCase::UPPERCASE:
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

	/**
	 * @used-by self::css()
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::customCss()
	 */
	function enabled():bool {return $this->b();}

	/**
	 * @used-by self::css()
	 * @used-by self::link()
	 * @return string
	 */
	function family():string {return df_first($this->familyA());}

	/**
	 * 2015-12-25
	 * @used-by self::link()
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::customCss()
	 */
	function familyIsStandard():bool {return 'default' === $this->familyS();}

	/**
	 * 2015-12-16 http://stackoverflow.com/questions/4659345
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
	 */
	function link():string {return dfc($this, function() {return $this->familyIsStandard() ? '' :
		'//fonts.googleapis.com/css?family=' . urlencode($this->family())
	;});}

	/**
	 * 2015-12-16
	 * @return string
	 */
	function weight() {return dfc($this, function() {return
		$this->variantNumber() ?: ($this->bold() ? 'bold' : 'normal')
	;});}

	/** @return bool */
	private function bold() {return $this->b();}
	/**
	 * 2015-12-16
	 * @return string[]
	 */
	private function familyA() {return dfc($this, function() {return explode(':', $this->familyS());});}

	/** @return string */
	private function familyS() {return $this[self::family];}

	/** @return bool */
	private function italic() {return $this->b();}

	/** @used-by self::css() */
	private function letter_case():bool {return $this->v();}

	/** @used-by self::css() */
	private function letter_spacing():Size {return $this->size();}

	/** @used-by self::css() */
	private function needScale():bool {return
		100 !== intval($this->scale_horizontal()) || 100 !== intval($this->scale_vertical())
	;}

	/**
	 * @used-by self::needScale()
	 * @used-by self::scaleRule()
	 */
	private function scale_horizontal():float {return $this->f();}

	/**
	 * 2015-12-16
	 * @used-by self::css()
	 */
	private function scaleRule():string {return dfc($this, function() {return sprintf('scale(%.2f,%.2f)',
		$this->scale_horizontal() / 100, $this->scale_vertical() / 100
	);});}

	/**
	 * @used-by self::needScale()
	 * @used-by self::scaleRule()
	 */
	private function scale_vertical():float {return $this->f();}

	/**
	 * 2015-12-16
	 * @used-by self::css()
	 * @used-by self::letter_spacing()
	 */
	private function size():Size {return dfc($this, function($key) {return new Size($this[$key]);}, [df_caller_f()]);}

	/**
	 * 2015-12-16
	 * @used-by self::css()
	 */
	private function style():string {return dfc($this, function() {return dfa(
		['regular' => 'normal', 'italic' => 'italic']
		, $this->variantWord()
		, $this->italic() ? 'italic' : ''
	);});}

	/** @used-by self::css() */
	private function underline():bool {return $this->b();}

	/**
	 * 2015-12-16
	 * @return string
	 */
	private function variant() {return dfc($this, function() {return dfa($this->familyA(), 1, '');});}

	/**
	 * 2015-12-16 Вычленяет «700» из «700italic»
	 * @return string
	 */
	private function variantNumber() {return dfc($this, function() {return df_nts(df_preg_int(
		'#\d+#', $this->variant()
	));});}

	/**
	 * 2015-12-16 ычленяет «italic» из «italic»
	 * @return string
	 */
	private function variantWord() {return dfc($this, function() {return str_replace(
		$this->variantNumber(), '', $this->variant()
	);});}

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