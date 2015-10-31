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
	public function action() {return $this->_action;}

	/**
	 * 2015-08-13
	 * @used-by \Df\Framework\App\ActionInterfacePlugin::beforeDispatch()
	 * @param ActionInterface|null $value
	 * @return void
	 */
	public function actionSet(ActionInterface $value) {$this->_action = $value;}

	/**
	 * Свойство, экранное название которого в данный момент переводится.
	 * 2015-09-20
	 * @used-by \Dfr\Translation\Realtime\Dictionary::handleForAttribute()
	 * @return AbstractAttribute|null
	 */
	public function attribute() {return $this->_attribute;}

	/**
	 * 2015-09-20
	 * @used-by \Df\Eav\Model\Entity\Attribute\Frontend\AbstractFrontendPlugin::afterGetLabel()
	 * @param AbstractAttribute $attribute
	 * @return void
	 */
	public function attributeSet(AbstractAttribute $attribute) {
		$this->_attribute = $attribute;
	}

	/**
	 * 2015-09-20
	 * @used-by \Df\Eav\Model\Entity\Attribute\Frontend\AbstractFrontendPlugin::afterGetLabel()
	 * @return void
	 */
	public function attributeUnset() {$this->_attribute = null;}

	/**
	 * 2015-08-13
	 * @return BlockInterface|AbstractBlock|null
	 */
	public function block() {return rm_last($this->_blockStack);}

	/**
	 * 2015-08-13
	 * @used-by \Df\Framework\View\TemplateEngineInterfacePlugin::aroundRender()
	 * @param BlockInterface|null $block
	 * @param string|null $templateFile
	 * @return void
	 */
	public function blockSet(BlockInterface $block, $templateFile) {
		$this->_blockStack[]= $block;
		$this->_templateFileStack[]= $templateFile;
	}

	/**
	 * 2015-09-02
	 * @used-by \Df\Framework\View\TemplateEngineInterfacePlugin::aroundRender()
	 * @return void
	 */
	public function blockSetPrev() {
		array_pop($this->_blockStack);
		array_pop($this->_templateFileStack);
	}

	/**
	 * @used-by \Df\Core\Observer::layoutGenerateBlocksBefore()
	 * @return void
	 */
	public function blocksGenerationStarted() {$this->_blocksGenerationStarted = true;}

	/**
	 * @used-by \Df\Core\Observer::layoutGenerateBlocksAfter()
	 * @return void
	 */
	public function blocksHasBeenGenerated() {$this->_blocksHasBeenGenerated = true;}

	/**
	 * 2015-09-19
	 * @return UiComponentInterface|AbstractComponent|null
	 */
	public function component() {return rm_last($this->_componentStack);}

	/**
	 * 2015-09-19
	 * @used-by \Df\Framework\View\TemplateEngineInterfacePlugin::aroundRender()
	 * @param UiComponentInterface|AbstractComponent|null $component
	 * @return void
	 */
	public function componentSet(UiComponentInterface $component) {
		$this->_componentStack[]= $component;
	}

	/**
	 * 2015-09-19
	 * @used-by \Df\Framework\View\TemplateEngineInterfacePlugin::aroundRender()
	 * @return void
	 */
	public function componentSetPrev() {
		array_pop($this->_componentStack);
	}

	/**
	 * @used-by rm_controller()
	 * @return \Magento\Framework\App\Action\Action|null
	 */
	public function controller() {return $this->_controller;}

	/**
	 * @used-by \Df\Core\Observer::controllerActionPredispatch()
	 * @param \Magento\Framework\App\Action\Action $controller
	 * @return void
	 */
	public function controllerSet(\Magento\Framework\App\Action\Action $controller) {
		$this->_controller = $controller;
	}

	/** @return bool */
	public function hasBlocksBeenGenerated() {return $this->_blocksHasBeenGenerated;}

	/** @return bool */
	public function hasBlocksGenerationBeenStarted() {return $this->_blocksGenerationStarted;}

	/**
	 * 2015-09-27
	 * @used-by \Df\Framework\View\Page\TitlePlugin::aroundGet()
	 * @used-by \Dfr\Translation\Realtime\Dictionary::handleForController()
	 * @param bool|null $state [optional]
	 * @return bool
	 */
	public function renderingTitle($state = null) {
		if (!is_null($state)) {
			$this->_renderingTitle = $state;
		}
		return $this->_renderingTitle;
	}

	/** @return bool */
	public function storeInitialized() {
		/** @var bool $result */
		static $result = false;
		if (!$result) {
			try {
				rm_store();
				$result = true;
			}
			catch (\Exception $e) {}
		}
		return $result;
	}

	/**
	 * @param bool $needThrow [optional]
	 * @return \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store|null
	 * @throws \Df\Core\Exception|\Exception
	 */
	public function storeProcessed($needThrow = true) {
		if (!isset($this->_storeProcessed)) {
			/** @var \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store|null $result */
			$result = null;
			if (rm_store_m()->hasSingleStore()) {
				/**
				 * 2015-08-10
				 * Нельзя использовать здесь @see rm_store(),
				 * потому что @see rm_store() сам использует @see storeProcessed(), и получится зависание.
				 */
				$result = rm_store_m()->getStore(true);
			}
			else {
				/**
				 * Если в системе присутствует больше одного магазина,
				 * то администратор должен указать обрабатываемый магазин
				 * параметром в запрашиваемом адресе одним из двух способов:
				 *
				 * 1) http://localhost.com:686/df-1c/cml2/index/?store-view=store_686
				 * 2) http://localhost.com:686/df-1c/cml2/index/store-view/store_686/
				 */
				/** @var string $storeCode */
				$storeCode = rm_request('store-view');
				if (is_null($storeCode)) {
					$storeCode = rm_preg_match(
						'#\/store\-view\/([^\/]+)\/#u', rm_ruri(), $needThrow = false
					);
				}
				if (!$storeCode) {
					if ($needThrow) {
						df_error(
							'Ваша система содержит несколько витрин,'
							. ' поэтому Вы должны указать системное имя обрабатываемой витрины'
							. ' в веб-адресе, добавив к веб-адресу окончание'
							. ' «/store-view/<системное имя витрины>/».'
						);
					}
				}
				else {
					df_assert_string_not_empty($storeCode);
					try {
						/**
						 * 2015-08-10
						 * Нельзя использовать здесь @see rm_store(),
						 * потому что @see rm_store() сам использует @see getStoreProcessed(), и получится зависание.
						 */
						$result = rm_store_m()->getStore($storeCode);
					}
					catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
						if ($needThrow) {
							df_error(
								'Витрина с системным именем «%s» отсутствует в Вашей системе.'
								, $storeCode
							);
						}
					}
				}
			}
			if (!$result) {
				df_assert(!$needThrow);
			}
			else {
				df_assert($result instanceof \Magento\Store\Api\Data\StoreInterface);
				if (!$result->getWebsiteId()) {
					// Так бывает...
					$result = rm_om()->create('Magento\Store\Model\Store');
					$result->load($result->getId());
					df_assert($result->getWebsiteId());
				}
			}
			$this->_storeProcessed = rm_n_set($result);
		}
		return rm_n_get($this->_storeProcessed);
	}

	/**
	 * 2015-08-14
	 * @param \Magento\Store\Api\Data\StoreInterface $value
	 * @return void
	 */
	public function storeProcessedSet(\Magento\Store\Api\Data\StoreInterface $value) {
		$this->_storeProcessed = $value;
	}

	/**
	 * 2015-08-13
	 * @return BlockInterface
	 */
	public function templateFile() {return rm_last($this->_templateFileStack);}

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
	private $_blockStack = array();

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
	private $_componentStack = array();

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
	 * 2015-08-13
	 * 2015-09-02
	 * Значение по умолчанию null можно не указывать.
	 * @used-by State::storeProcessed()
	 * @used-by State::storeProcessedSet()
	 * @var \Magento\Store\Api\Data\StoreInterface|null
	 */
	private $_storeProcessed;

	/**
	 * 2015-09-02
	 * @used-by State::blockSet()
	 * @used-by State::blockSetPrev()
	 * @used-by State::templateFile()
	 * @var array(string|null)
	 */
	private $_templateFileStack = array();

	/** @return \Df\Core\State */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}