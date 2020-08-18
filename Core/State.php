<?php
namespace Df\Core;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\AbstractComponent;
// 2015-08-13
final class State {
	/**
	 * 2015-10-31
	 * @used-by \Df\Core\Observer\LayoutGenerateBlocksBefore::execute()
	 */
	function blocksGenerationStarted() {$this->_blocksGenerationStarted = true;}

	/**
	 * 2015-10-31
	 * @used-by \Df\Core\Observer\LayoutGenerateBlocksAfter::execute()
	 */
	function blocksHasBeenGenerated() {$this->_blocksHasBeenGenerated = true;}

	/**
	 * @used-by df_controller()
	 * @return \Magento\Framework\App\Action\Action|null
	 */
	function controller() {return $this->_controller;}

	/**
	 * @used-by \Df\Core\Observer\ControllerActionPredispatch::execute()
	 * @param \Magento\Framework\App\Action\Action $controller
	 */
	function controllerSet(\Magento\Framework\App\Action\Action $controller) {
		$this->_controller = $controller;
	}

	/**
	 * 2015-09-27
	 * @used-by \Df\Framework\Plugin\View\Page\Title::aroundGet()
	 * @used-by \Dfr\Core\Realtime\Dictionary::handleForController()
	 * @param bool|null $state [optional]
	 * @return bool
	 */
	function renderingTitle($state = null) {
		if (!is_null($state)) {
			$this->_renderingTitle = $state;
		}
		return $this->_renderingTitle;
	}

	/**
	 * 2015-08-13
	 * @used-by \Dfr\Core\Realtime\Dictionary::handleForBlock()
	 * @return BlockInterface
	 */
	function templateFile() {return df_last($this->_templateFileStack);}

	/**
	 * 2015-09-20
	 * @used-by State::attributeSet()
	 * @used-by State::attributeUnset()
	 * @var AbstractAttribute
	 */
	private $_attribute;

	/**
	 * 2015-08-13
	 * @used-by State::block()
	 * @used-by State::blockSet()
	 * @used-by State::blockSetPrev()
	 * @var array(BlockInterface|null)
	 */
	private $_blockStack = [];

	/** @var bool */
	private $_blocksGenerationStarted = false;

	/** @var bool */
	private $_blocksHasBeenGenerated = false;

	/**
	 * 2015-09-19
	 * @used-by State::component()
	 * @used-by State::componentSet()
	 * @used-by State::componentSetPrev()
	 * @var array(UiComponentInterface|AbstractComponent|null)
	 */
	private $_componentStack = [];

	/**
	 * 2015-09-02
	 * Значение по умолчанию null можно не указывать.
	 * @var \Magento\Framework\App\Action\Action|null
	 */
	private $_controller;

	/**
	 * 2015-09-27
	 * @used-by State::renderingTitle()
	 * @var bool
	 */
	private $_renderingTitle = false;

	/**
	 * 2015-09-02
	 * @used-by State::blockSet()
	 * @used-by State::blockSetPrev()
	 * @used-by State::templateFile()
	 * @var array(string|null)
	 */
	private $_templateFileStack = [];

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}