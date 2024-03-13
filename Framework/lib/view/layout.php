<?php
use Df\Theme\Model\View\Design as DfDesign;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\ProcessorInterface as IProcessor;
use Magento\Framework\View\LayoutInterface as ILayout;
use Magento\Framework\View\Model\Layout\Merge;

/**
 * @used-by df_block()
 * @used-by df_layout_update()
 * @used-by df_parent_name()
 * @used-by \KingPalm\B2B\Block\Registration::v()
 * @return Layout|ILayout
 */
function df_layout() {return df_o(ILayout::class);}

/**
 * 2017-10-16
 * @used-by df_handles()
 * @used-by \Df\Framework\Plugin\App\Action\AbstractAction::beforeExecute() 
 * @param Closure|bool|mixed $onError [optional]
 * @return IProcessor|Merge
 */
function df_layout_update($onError = true) {return df_try(function() {
	df_assert(DfDesign::isThemeInitialized(),
		'This attempt to call Magento\Framework\View\Layout::getUpdate() can break the Magento frontend.'
	);
	return df_layout()->getUpdate();
}, $onError);}

/**
 * 2016-11-30
 * Наивное $e->getParentBlock()->getNameInLayout() некорректно,
 * потому что родительским элементом для $e может быть не только блок,
 * но и контейнер, и тогда $e->getParentBlock() вернёт false.
 * @param AbstractBlock|string $e
 * @return string|null
 */
function df_parent_name($e) {return df_ftn(df_layout()->getParentName(
	$e instanceof AbstractBlock ? $e->getNameInLayout() : $e
));}