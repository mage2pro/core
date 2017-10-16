<?php
namespace Df\Framework\View2;
use Df\Core\Exception as DFE;
use Magento\Framework\View\Layout as mLayout;
use Magento\Framework\View\LayoutInterface as ILayout;
// 2017-10-16
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Layout extends \Magento\Framework\View\Layout {
	/**            
	 * 2017-10-16 
	 * @see \Magento\Framework\View\Layout::getUpdate()
	 * @param \Closure|bool|mixed $onError [optional]
	 * @return bool
	 * @throws DFE
	 */
	final static function update($onError = true) {return df_try(function() {
		$l = df_layout(); /** @var ILayout|mLayout $l */
		df_assert($l->_update,
			'This attempt to call Magento\Framework\View\Layout::getUpdate() can break the Magento frontend.'
		);
		return $l->_update;
	}, $onError);}
}