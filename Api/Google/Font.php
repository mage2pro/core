<?php
namespace Df\Api\Google;
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
	 * 2015-11-28
		"variants": [
			"regular",
			"italic"
		]
	 * @return string
	 */
	public function variants() {return $this['variants'];}
}