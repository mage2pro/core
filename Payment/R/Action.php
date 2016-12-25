<?php
// 2016-12-25
namespace Df\Payment\R;
use Df\Payment\Settings as S;
abstract class Action extends \Df\Framework\Controller\Action {
	/**
	 * 2016-12-25
	 * @return bool
	 */
	protected function needLog() {return $this->s()->logConfirmation();}

	/**
	 * 2016-12-25
	 * @return S
	 */
	protected function s() {return dfc($this, function() {return S::conventionB(static::class);});}
}