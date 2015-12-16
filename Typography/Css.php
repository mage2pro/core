<?php
namespace Df\Typography;
use Df\Config\Source\LetterCase;
class Css extends \Df\Core\O {
	/**
	 * 2015-12-16
	 * @return array(string => string[])|\Traversable
	 */
	private function blocks() {
		if (!isset($this->{__METHOD__})) {
			$this->build();
			$this->{__METHOD__} = true;
		}
		return $this->_blocks;
	}

	/** @return void */
	private function build() {
		$this->rule('font-weight', $this->font()->weight());
		$this->rule('color', $this->font()->color());
		$this->rule('font-style', $this->font()->style());
		if ($this->font()->underline()) {
			$this->rule('text-decoration', 'underline');
		}
		switch ($this->font()->letter_case()) {
			case LetterCase::$LOWERCASE:
				$this->rule('text-transform', 'lowercase');
				break;
			case LetterCase::$UCFIRST:
				/**
				 * 2015-11-14
				 * .link { text-transform: lowercase; }
				 * .link:first-letter {text-transform: uppercase;}
				 * http://stackoverflow.com/a/10256138
				 */
				$this->rule('text-transform', 'lowercase');
				$this->rule('text-transform', 'uppercase', ':first-letter');
				break;
			case LetterCase::$UCWORDS:
				$this->rule('text-transform', 'capitalize');
				break;
			case LetterCase::$UPPERCASE:
				$this->rule('text-transform', 'uppercase');
				break;
		}
		if ($this->font()->letter_spacing()->value()) {
			$this->rule('letter-spacing', $this->font()->letter_spacing());
		}
		if ($this->font()->needScale()) {
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
				$this->rule($name, $this->font()->scaleRule());
			}
		}
		if ($this->font()->size()->value()) {
			$this->rule('font-size', $this->font()->size());
		}
		if ('default' !== $this->font()->family()) {
			$this->rule('font-family', df_quote_single($this->font()->family()));
		}
	}

	/** @return Font */
	private function font() {return $this[self::$P__FONT];}

	/**
	 * 2015-12-16
	 * @return string
	 */
	private function render() {
		return df_concat_n(df_map(
			/**
			 * @param string $selector
			 * @param string[] $rules
			 * @return string
			 */
			function($selector, $rules) {
				/** @var string $rulesS */
				$rulesS = df_tab_multiline(df_concat_n($rules));
				return "{$selector} {\n{$rulesS}\n}";
			}
			, $this->blocks(), [], [], RM_BEFORE
		));
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $selectorSuffix [optional]
	 * @return void
	 */
	private function rule($name, $value, $selectorSuffix = '') {
		if ('' !== $value && false !== $value) {
			$this->_blocks[$this->selector() . $selectorSuffix][]=
				"{$name}: {$value} !important;"
			;
		}
	}

	/** @return string */
	private function selector() {return $this[self::$P__SELECTOR];}

	/**
	 * 2015-12-16
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__FONT, Font::class)
			->_prop(self::$P__SELECTOR, RM_V_STRING_NE)
		;
	}

	/** @var string[] */
	private $_blocks = [];

	/** @var string */
	private static $P__FONT = 'font';
	/** @var string */
	private static $P__SELECTOR = 'SELECTOR';
	/**
	 * 2015-12-16
	 * @param Font $font
	 * @param string $selector
	 * @return string
	 */
	public static function r(Font $font, $selector) {
		/** @var string $result */
		if (!$font->enabled()) {
			$result = '';
		}
		else {
			/** @var Css $i */
			$i = new self([self::$P__FONT => $font, self::$P__SELECTOR => $selector]);
			$result = $i->render();
		}
		return $result;
	}
}