<?php
namespace Df\Core\Model\Format\Html;
class Tag extends \Df\Core\O {
	/** @return string */
	private function _render() {
		return strtr(
			!$this->content() && $this->isShortTagAllowed()
			? '<{tag-and-attributes}/>'
			: '<{tag-and-attributes}{after-attributes}>{content}</{tag}>'
			,array(
				'{tag}' => $this->tag()
				,'{tag-and-attributes}' => $this->openTagWithAttributesAsText()
				,'{after-attributes}' => $this->shouldAttributesBeMultiline() ? "\n" : ''
				,'{content}' => $this->content()
			)
		);
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by attributesAsText()
	 * @used-by array_map()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $name
	 * @param string|string[]|int|null $value
	 * @return string
	 */
	private function attributeAsText($name, $value) {
		df_param_string_not_empty($name, 0);
		// 2015-04-16
		// Передавать в качестве $value массив имеет смысл, например, для атрибута «class».
		if (is_array($value)) {
			$value = implode(' ', array_filter($value));
		}
		$value = df_e($value);
		return '' === $value ? '' : "{$name}='{$value}'";
	}
	
	/** @return array(string => string) */
	private function attributes() {return $this->cfg(self::$P__ATTRIBUTES, array());}

	/** @return string */
	private function attributesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(
				$this->shouldAttributesBeMultiline() ? "\n" :  ' '
				, df_clean(array_map(
					/** @uses \Df\Core\Model\Format\Html\Tag::attributeAsText() */
					array($this, 'attributeAsText')
					,array_keys($this->attributes())
					,array_values($this->attributes())
				))
			);
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function content() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->cfg(self::$P__CONTENT, '');
			$result = df_trim($result, "\n");
			/** @var bool $isMultiline */
			$isMultiline = df_contains($result, "\n");
			if ($isMultiline) {
				$result = "\n" . df_tab_multiline($result) . "\n";
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isShortTagAllowed() {
		return !in_array(strtolower($this->tag()), array('div', 'script'));
	}
	
	/** @return string */
	private function openTagWithAttributesAsText() {
		return df_concat_clean(' '
			,$this->tag()
			,$this->shouldAttributesBeMultiline() ? "\n" : null
			,call_user_func(
				$this->shouldAttributesBeMultiline() ? 'df_tab_multiline' : 'df_nop'
				,$this->attributesAsText()
			)
		);
	}

	/** @return bool */
	private function shouldAttributesBeMultiline() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (1 < count($this->attributes()));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function tag() {return $this[self::$P__TAG];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__TAG, RM_V_STRING_NE)
			->_prop(self::$P__CONTENT, RM_V_STRING, false)
			->_prop(self::$P__ATTRIBUTES, RM_V_ARRAY, false)
		;
	}
	/** @var string */
	private static $P__ATTRIBUTES = 'attributes';
	/** @var string */
	private static $P__CONTENT = 'content';
	/** @var string */
	private static $P__TAG = 'tag';

	/**
	 * @param string $href
	 * @return string
	 */
	public static function cssExternal($href) {
		df_param_string_not_empty($href, 0);
		return self::render(
			'link', array('rel' => 'stylesheet', 'type' => 'text/javascript', 'href' => $href)
		);
	}

	/**
	 * @param string $tag
	 * @param array(string => string) $attributes [optional]
	 * @param string $content [optional]
	 * @return string
	 */
	public static function render($tag, array $attributes = [], $content = null) {
		/** @var Tag $i */
		$i = new self(array(
			self::$P__ATTRIBUTES => $attributes
			,self::$P__CONTENT => $content
			,self::$P__TAG => $tag
		));
		return $i->_render();
	}

	/**
	 * @param string $src
	 * @return string
	 */
	public static function scriptExternal($src) {
		df_param_string_not_empty($src, 0);
		return self::render('script', array('type' => 'text/javascript', 'src' => $src));
	}

	/**
	 * @param string $code
	 * @return string
	 */
	public static function scriptLocal($code) {
		df_param_string_not_empty($code, 0);
		return self::render('script', array('type' => 'text/javascript'), $code);
	}
}