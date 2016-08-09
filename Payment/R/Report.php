<?php
namespace Df\Payment\R;
// 2016-07-09
class Report extends \Df\Core\O {
	/**
	 * 2016-07-10
	 * @return array(string => string)
	 */
	public function asArray() {
		if (!isset($this->{__METHOD__})) {$this->{__METHOD__} = df_map_k(
			function($key, $value) {return $this->formatKV($key, $value);}
			,$this->primary() + [
				'Request URL'  => $this->response()->requestUrl()
				,'Request params' => df_tab_multiline(df_print_params($this->response()->requestParams()))
				,'Response' => df_tab_multiline(df_print_params($this->response()->getData()))
			]
		);}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @return string
	 */
	public function asText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_n($this->asArray());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @return array(string => string)
	 */
	protected function primary() {return [];}

	/**
	 * 2016-07-10
	 * @return string[]
	 */
	protected function keysToSuppress() {return [];}

	/**
	 * 2016-07-10
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	private function formatKV($key, $value) {
		return
			in_array($key, $this->keysToSuppress())
			? $value
			// 2016-07-13
			// Раньше тут было более сложное выражение:
			// sprintf("{$key}: %s.", df_trim($value, '.'))
			: "{$key}: {$value}"

		;
	}

	/** @return Response */
	private function response() {return $this[self::$P__RESPONSE];}

	/**
	 * 2016-07-10
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__RESPONSE, Response::class);
	}

	/** @var string */
	private static $P__RESPONSE = 'response';

	/**
	 * 2016-07-10
	 * @param string $class
	 * @param Response $r
	 * @return self
	 */
	public static function ic($class, Response $r) {
		return df_ic($class, __CLASS__, [self::$P__RESPONSE => $r]);
	}
}