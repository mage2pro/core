<?php
namespace Df\API;
use Df\Core\O;
# 2019-04-05
final class FacadeOptions {
	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImage::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Get::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Validate::p()
	 * @param string|null|string $v [optional]
	 * @return string|self
	 */
	function resC($v = DF_N) {return df_prop($this, $v, O::class);}

	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
	 * @param bool|null|string $v [optional]
	 * @return bool|self
	 */
	function silent($v = DF_N) {return df_prop($this, $v);}

	/**
	 * 2019-04-05
	 * @used-by i()
	 */
	private function __construct() {}

	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::opts()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImage::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Get::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Validate::p()
	 */
	static function i():self {return new self;}
}