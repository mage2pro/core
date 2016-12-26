<?php
// 2016-07-09
namespace Df\Payment\Webhook;
class Report extends \Df\Core\O {
	/**
	 * 2016-07-10
	 * @return array(string => string)
	 */
	public function asArray() {return dfc($this, function() {return df_map_k(
		function($key, $value) {return $this->formatKV($key, $value);}
		,$this->primary() + [
			'Request URL'  => $this->response()->requestUrl()
			,'Request params' => df_tab_multiline(df_print_params($this->response()->requestP()))
			,'Response' => df_tab_multiline(df_print_params($this->response()->getData()))
		]
	);});}

	/**
	 * 2016-07-10
	 * @return string
	 */
	public function asText() {return dfc($this, function() {return df_cc_n($this->asArray());});}

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
	private function formatKV($key, $value) {return
		df_ccc(': ', in_array($key, $this->keysToSuppress()) ? null : $key, $value)
	;}

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
	public static function ic($class, Response $r) {return df_ic(
		$class, __CLASS__, [self::$P__RESPONSE => $r]
	);}
}