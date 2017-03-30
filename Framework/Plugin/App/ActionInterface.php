<?php
namespace Df\Framework\Plugin\App;
use Magento\Framework\App\ActionInterface as Sb;
final class ActionInterface {
	/**
	 * @param Sb $sb
	 */
	function beforeDispatch(Sb $sb) {df_state()->actionSet($sb);}
}