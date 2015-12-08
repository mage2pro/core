<?php
namespace Df\Api\Google;
use Df\Api\Google\Font\Variant;
/**
 * 2015-11-27
 * https://developers.google.com/fonts/docs/developer_api#Example
		{
			"kind": "webfonts#webfont",
			"family": "ABeeZee",
			"category": "sans-serif",
			"variants": [
				"regular",
				"italic"
			],
			"subsets": [
				"latin"
			],
			"version": "v4",
			"lastModified": "2015-04-06",
			"files": {
				"regular": "http://fonts.gstatic.com/s/abeezee/v4/mE5BOuZKGln_Ex0uYKpIaw.ttf",
				"italic": "http://fonts.gstatic.com/s/abeezee/v4/kpplLynmYgP0YtlJA3atRw.ttf"
			}
		},
		{
			"kind": "webfonts#webfont",
			"family": "Abel",
			"category": "sans-serif",
			"variants": [
				"regular"
			],
			"subsets": [
				"latin"
			],
			"version": "v6",
			"lastModified": "2015-04-06",
			"files": {
				"regular": "http://fonts.gstatic.com/s/abel/v6/RpUKfqNxoyNe_ka23bzQ2A.ttf"
			}
		}
 */
class Font extends \Df\Core\O {
	/**
	 * 2015-11-28
	 * "family": "ABeeZee"
	 * @return string
	 */
	public function family() {return $this['family'];}

	/**
	 * 2015-11-29
	 * @param string $name
	 * @return Variant
	 * @throws \Exception
	 */
	public function variant($name) {
		/** @var Variant|null $result */
		$result = df_a($this->variants(), $name);
		if (!$result) {
			throw new \Exception("Variant «{$name}» of font «{$this->family()}» is not found.");
		}
		return $result;
	}

	/**
	 * 2015-11-28
		"variants": [
			"regular",
			"italic"
		]
	 * @return string
	 */
	public function variantNames() {return $this['variants'];}

	/**
	 * 2015-11-27
	 * @return array(string => Variant)
	 */
	public function variants() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_combine($this->variantNames(), array_map(function($name) {
				return Variant::i($this, $name, $this['files'][$name]);
			}, $this->variantNames()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @return array(string => Variant)
	 */
	public function variantsAvailable() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_filter($this->variants(), function(Variant $variant) {
				return $variant->preview()->isAvailable();
			});
		}
		return $this->{__METHOD__};
	}
}