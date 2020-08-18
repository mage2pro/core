<?php
namespace Df\Core;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\View\Element\BlockInterface;
// 2015-08-13
final class State {
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

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}