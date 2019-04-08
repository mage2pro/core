<?php
namespace Df\API;
use Df\API\Document as D;
// 2019-04-05
final class FacadeOptions {
	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImage::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Get::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Validate::p()
	 * @param string|null $v [optional]
	 * @return string|$this
	 */
	function resC($v = null) {return df_prop($this, $v, D::class);}

	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
	 * @param bool|null $v [optional]
	 * @return bool|$this
	 */
	function silent($v = null) {return df_prop($this, $v);}

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
	 * @return FacadeOptions
	 */
	static function i() {return new self;}
}