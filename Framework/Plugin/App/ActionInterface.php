<?php
namespace Df\Framework\Plugin\App;
use Magento\Framework\App\ActionInterface as Sb;
class ActionInterface {
	/**
	 * @param Sb $sb
	 * @return void
	 */
	function beforeDispatch(Sb $sb) {df_state()->actionSet($sb);}
}