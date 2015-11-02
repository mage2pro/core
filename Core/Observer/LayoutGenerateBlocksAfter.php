<?php
namespace Df\Core\Observer;
use Magento\Framework\Event\ObserverInterface;
class LayoutGenerateBlocksAfter implements ObserverInterface {
	/**
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @see \Magento\Framework\View\Layout\Builder::generateLayoutBlocks()
	 * https://github.com/magento/magento2/blob/dd47569249206b217e0a9f9a9371e73fd7622724/lib/internal/Magento/Framework/View/Layout/Builder.php#L133
		$this->eventManager->dispatch(
			'layout_generate_blocks_after',
			['full_action_name' => $this->request->getFullActionName(), 'layout' => $this->layout]
		);
	 * @param \Magento\Framework\Event\Observer $o
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $o) {
		df_state()->blocksHasBeenGenerated();
	}
}