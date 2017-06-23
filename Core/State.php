<?php
namespace Df\Core;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\AbstractComponent;
class State {
	/**
	 * 2015-08-13
	 * @return ActionInterface
	 */
	function action() {return $this->_action;}

	/**
	 * 2015-08-13
	 * @used-by \Df\Framework\Plugin\App\ActionInterface::beforeDispatch()
	 * @param ActionInterface|null $value
	 */
	function actionSet(ActionInterface $value) {$this->_action = $value;}

	/**
	 * Свойство, экранное название которого в данный момент переводится.
	 * 2015-09-20
	 * @used-by \Dfr\Core\Realtime\Dictionary::handleForAttribute()
	 * @return AbstractAttribute|null
	 */
	function attribute() {return $this->_attribute;}

	/**
	 * 2015-09-20
	 * @used-by \Df\Eav\Plugin\Model\Entity\Attribute\Frontend\AbstractFrontend::afterGetLabel()
	 * @param AbstractAttribute $attribute
	 */
	function attributeSet(AbstractAttribute $attribute) {$this->_attribute = $attribute;}

	/**
	 * 2015-09-20
	 * @used-by \Df\Eav\Plugin\Model\Entity\Attribute\Frontend\AbstractFrontend::afterGetLabel()
	 */
	function attributeUnset() {$this->_attribute = null;}

	/**
	 * 2015-08-13
	 * @return BlockInterface|AbstractBlock|null
	 */
	function block() {return df_last($this->_blockStack);}

	/**
	 * 2015-08-13
	 * @used-by \Df\Framework\Plugin\View\TemplateEngineInterface::aroundRender()
	 * @param BlockInterface|null $block
	 * @param string|null $templateFile
	 */
	function blockSet(BlockInterface $block, $templateFile) {
		$this->_blockStack[]= $block;
		$this->_templateFileStack[]= $templateFile;
	}

	/**
	 * 2015-09-02
	 * @used-by \Df\Framework\Plugin\View\TemplateEngineInterface::aroundRender()
	 */
	function blockSetPrev() {
		array_pop($this->_blockStack);
		array_pop($this->_templateFileStack);
	}

	/**
	 * @used-by \Df\Core\Observer::layoutGenerateBlocksBefore()
	 */
	function blocksGenerationStarted() {$this->_blocksGenerationStarted = true;}

	/**
	 * @used-by \Df\Core\Observer::layoutGenerateBlocksAfter()
	 */
	function blocksHasBeenGenerated() {$this->_blocksHasBeenGenerated = true;}

	/**
	 * 2015-09-19
	 * @return UiComponentInterface|AbstractComponent|null
	 */
	function component() {return df_last($this->_componentStack);}

	/**
	 * 2015-09-19
	 * @used-by \Df\Framework\Plugin\View\TemplateEngineInterface::aroundRender()
	 * @param UiComponentInterface|AbstractComponent|null $component
	 */
	function componentSet(UiComponentInterface $component) {$this->_componentStack[]= $component;}

	/**
	 * 2015-09-19
	 * @used-by \Df\Framework\Plugin\View\TemplateEngineInterface::aroundRender()
	 */
	function componentSetPrev() {array_pop($this->_componentStack);}

	/**
	 * @used-by df_controller()
	 * @return \Magento\Framework\App\Action\Action|null
	 */
	function controller() {return $this->_controller;}

	/**
	 * @used-by \Df\Core\Observer::controllerActionPredispatch()
	 * @param \Magento\Framework\App\Action\Action $controller
	 */
	function controllerSet(\Magento\Framework\App\Action\Action $controller) {
		$this->_controller = $controller;
	}

	/** @return bool */
	function hasBlocksBeenGenerated() {return $this->_blocksHasBeenGenerated;}

	/** @return bool */
	function hasBlocksGenerationBeenStarted() {return $this->_blocksGenerationStarted;}

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

	/** @return bool */
	function storeInitialized() {
		/** @var bool $result */
		static $result = false;
		if (!$result) {
			try {
				df_store();
				$result = true;
			}
			catch (\Exception $e) {}
		}
		return $result;
	}

	/**
	 * 2015-08-13
	 * @return BlockInterface
	 */
	function templateFile() {return df_last($this->_templateFileStack);}

	/**
	 * 2015-08-13
	 * @used-by State::action()
	 * @used-by State::actionSet()
	 * @var ActionInterface|null
	 */
	private $_action;

	/**
	 * 2015-09-20
	 * @used-by State::attribute()
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