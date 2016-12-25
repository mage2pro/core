<?php
// 2016-12-25
namespace Df\Payment\R;
use Df\Payment\Settings as S;
abstract class Action extends \Df\Framework\Controller\Action {
	/**
	 * 2016-12-25
	 * @return bool
	 */
	protected static function needLog() {return self::s()->logConfirmation();}

	/**
	 * 2016-12-25
	 * @return S
	 */
	protected static function s() {return dfcf(function() {return S::conventionB(static::class);});}
}