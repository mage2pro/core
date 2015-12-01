<?php
namespace Df\Api\Google;
use Df\Api\Settings\Google as Settings;
class Fonts extends \Df\Core\O implements \IteratorAggregate, \Countable {
	/**
	 * 2015-11-27
	 * @override
	 * @see \Countable::count()
	 * @return int
	 */
	public function count() {return count($this->items());}

	/**
	 * 2015-11-29
	 * @param string $family
	 * @return Font
	 * @throws \Exception
	 */
	public function get($family) {
		/** @var Font|null $result */
		$result = df_a($this->items(), $family);
		if (!$result) {
			throw new \Exception("Font family is not found: «{$family}».");
		}
		return $result;
	}

	/**
	 * 2015-11-27
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	public function getIterator() {return new \ArrayIterator($this->items());}

	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Core\O::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {return self::m(__CLASS__, '_responseA');}

	/**
	 * 2015-11-27
	 * @return array(string => Font)
	 */
	private function items() {
		if (!isset($this->{__METHOD__})) {
			/** @var Font[] $fonts */
			$fonts = array_map(function(array $itemA) {return new Font($itemA);}, $this->responseA());
			/** @var string[] $families */
			$families = array_map(function(Font $font) {return $font->family();}, $fonts);
			$this->{__METHOD__} = array_combine($families, $fonts);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-27
	 * @return array(string => mixed)
	 * @throws \Exception
	 */
	private function responseA() {
		if (!isset($this->_responseA)) {
			/** @var bool $debug */
			$debug = true;
			/** @var array(string => mixed) $result */
			$result = json_decode(
				$debug
				? df_http_get('https://mage2.pro/google-fonts.json')
				: df_http_get('https://www.googleapis.com/webfonts/v1/webfonts', [
					'key' => Settings::s()->serverApiKey(), 'sort' => 'alpha'
				])
				, true
			);
			/**
			 * 2015-11-17
			 * В документации об этом ни слова не сказано,
			 * однако в случае сбоя Google API возвращает JSON следующией структуры:
				{
					error: {
						errors: [
							{
								domain: "usageLimits",
								reason: "accessNotConfigured",
								message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
								extendedHelp: "https://console.developers.google.com"
							}
						],
						code: 403,
						message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration."
					}
				}
			 * https://developers.google.com/fonts/docs/developer_api
			 */
			/** @var array(string => mixed)|null $result */
			$error = df_a($result, 'error');
			if ($error) {
				throw (new Exception\Font($error))->standard();
			}
			/**
			 * 2015-11-27
			 * https://developers.google.com/fonts/docs/developer_api#Example
			 */
			$result = df_a($result, 'items');
			df_result_array($result);
			$this->_responseA = $result;
		}
		return $this->_responseA;
	}

	/** @return string */
	public static function basePathAbsolute() {
		return df_media_path_absolute(self::basePathRelative());
	}

	/** @return string */
	public static function basePathRelative() {
		return df_concat_path('df', 'api', 'google', 'fonts') . '/';
	}

	/** @return \Df\Api\Google\Fonts */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}