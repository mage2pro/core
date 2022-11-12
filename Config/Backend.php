<?php
namespace Df\Config;
use Magento\Config\Model\Config\Structure\AbstractElement as ConfigElement;
use Magento\Config\Model\Config\Structure\Element\Section;
use Magento\Config\Model\Config\Structure\ElementInterface as IConfigElement;
use Magento\Framework\Phrase;
/**
 * 2016-08-03
 * Since Magento 2.1.0, a backend model is created
 * only if the `core_config_data` table has a `value` for the current `scope` and `scope_id`:
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form.php#L309-L327
 * If the `core_config_data` table does not have a `value` for the current `scope` and `scope_id`,
 * then Magento does not create a backend model, but it still retrieves a `value` from a parent (e.g. «default») scope:
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form.php#L323-L327
 * It seems to be a bug:
 * a `value` can be passed from the database to the configuration screen without a validation with the backend model.
 * In Magento < 2.1.0 a backend model is created in any case, and it seems to be correct:
 * https://github.com/magento/magento2/blob/2.0.8/app/code/Magento/Config/Block/System/Config/Form.php#L330-L342
 * @see \Df\Config\Backend\Checkbox
 * @see \Df\Config\Backend\Serialized
 * @method mixed|null getValue()
 * @method $this setStore($value)
 * @method $this setWebsite($value)
 */
class Backend extends \Magento\Framework\App\Config\Value {
	/**
	 * 2015-12-07
	 * Unfortunately, the following standard methods could leave the model in an inconsistent state in the case of an exception:
	 * @see \Magento\Framework\Model\AbstractModel::beforeSave()
	 * @see \Magento\Framework\Model\AbstractModel::afterSave()
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_beforeSave()
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_afterSave()
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_serializeFields()
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::unserializeFields()
	 * https://mage2.pro/t/283
	 * https://mage2.pro/t/284
	 * That is why I have implemented my own methods:
	 * @see dfSaveAfter()
	 * @see dfSaveBefore()
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\App\Config\Value::save()
	 * @used-by \Magento\Framework\DB\Transaction::save():
	 *		foreach ($this->_objects as $object) {
	 *			$object->save();
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/DB/Transaction.php#L127-L133
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/DB/Transaction.php#L127-L133
	 * @see \Magento\Config\Model\Config::save():
	 * 		$saveTransaction->save();
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Model/Config.php#L151
	 * @throws \Exception
	 */
	function save():self {
		try {$this->dfSaveBefore(); parent::save();}
		catch (\Exception $e) {
			df_log($e);
			/**
			 * 2017-09-27
			 * Previously, I had the following code here: throw df_lx($e);
			 * It triggered a false positive of the Magento Marketplace code validation tool:
			 * «Namespace for df_le class is not specified»:
			 * https://github.com/mage2pro/core/issues/27
			 * https://github.com/magento/marketplace-eqp/issues/45
			 * So I write it in the 2 lines as a workaround: $e = df_lx($e); throw $e;
			 * I use the same solution here: \Df\Payment\Method::action()
			 * 2017-10-19 Previously, I had the following code here: $e = df_lx($e); throw $e;
			 */
			df_message_error($e);
		}
		finally {
			/**
			 * 2017-12-04
			 * Note 1.
			 * It is important to call @uses dfSaveAfter() only after database commit.
			 * It fixes the issues like this:
			 * "«Please set your Moip private key in the Magento backend» even if the Moip private key is set"
			 * https://github.com/mage2pro/moip/issues/22
			 * Note 2.
			 * @used-by \Magento\Framework\Model\ResourceModel\AbstractResource::commit():
			 *		$this->getConnection()->commit();
			 *		if ($this->getConnection()->getTransactionLevel() === 0) {
			 *			$callbacks = CallbackPool::get(spl_object_hash($this->getConnection()));
			 *			try {
			 *				foreach ($callbacks as $callback) {
			 *					call_user_func($callback);
			 *				}
			 *			}
			 *			catch (\Exception $e) {
			 *				$this->getLogger()->critical($e);
			 *			}
			 *		}
			 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Model/ResourceModel/AbstractResource.php#L81-L103
			 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/Model/ResourceModel/AbstractResource.php#L82-L105
			 */
			$this->_getResource()->addCommitCallback(function() {$this->dfSaveAfter();});
		}
		return $this;
	}

	/**
	 * 2016-08-03
	 * @override
	 * @see \Magento\Framework\Model\AbstractModel::_afterLoad()
	 * @used-by \Magento\Framework\Model\AbstractModel::load()
	 * @see \Df\Config\Backend\Serialized::_afterLoad()
	 */
	protected function _afterLoad():void {
		parent::_afterLoad();
		/** 2017-12-12 @todo Should we care of a custom `config_path` or not? https://mage2.pro/t/5148 */
		self::$_processed[$this->getPath()] = true;
	}

	/**
	 * 2016-08-02
	 * @used-by \Df\Config\Backend\Serialized::processA()
	 */
	final protected function label():string {return dfc($this, function() {
		/** 2017-12-12 @todo Should we care of a custom `config_path` or not? https://mage2.pro/t/5148 */
		$pathA = explode('/', $this->getPath()); /** @var string[] $pathA */
		$resultA = []; /** @var Phrase[] $resultA */
		/** @var IConfigElement|ConfigElement|Section|null $e */
		while ($pathA && ($e = df_config_structure()->getElementByPathParts($pathA))) {
			$resultA[]= $e->getLabel();
			array_pop($pathA);
		}
		$resultA[]= df_config_tab_label($e);
		$resultA = array_reverse($resultA);
		$resultA[]= __($this->fc('label', false));
		return implode(' → ', df_quote_russian($resultA));
	});}

	/**
	 * 2015-12-07
	 * @used-by self::save()
	 * @used-by \Df\Config\Backend\Serialized::dfSaveAfter()
	 * @see \Df\Config\Backend\Serialized::dfSaveAfter()
	 * @see \Dfe\Moip\Backend\Enable::dfSaveAfter()
	 */
	protected function dfSaveAfter():void {}

	/**
	 * 2015-12-07
	 * @used-by self::save()
	 * @used-by \Df\Config\Backend\Checkbox::dfSaveBefore()
	 * @used-by \Df\Config\Backend\Serialized::dfSaveBefore()
	 * @see \Df\Config\Backend\Checkbox::dfSaveBefore()
	 * @see \Df\Config\Backend\Serialized::dfSaveBefore()
	 */
	protected function dfSaveBefore():void {}

	/**
	 * 2016-07-31
	 * 2020-02-02
	 * I have implemented a workaround for a custom config path in @uses df_config_field()
	 * "Magento\Config\Model\Config\Structure\AbstractElement::getPath() ignores a custom `config_path` value"
	 * https://mage2.pro/t/5148
	 * @used-by self::label()
	 * @used-by \Df\Config\Backend\Serialized::entityC()
	 * @param string $k
	 * @param bool $req [optional]
	 * @return string|null
	 */
	final protected function fc($k, $req = true) {
		$r = dfad(df_config_field($this), $k); /** @var string|null $r */
		if ($req && is_null($r)) {
			# 2020-07-08
			# "[df_assert_class_exists] The argument «unknown» is rejected by the «df_param_sne» validator":
			# https://github.com/mage2pro/core/issues/105
			# I have improved the diagnostic message.
			df_error("The required parameter `$k` is absent for the `{$this->getPath()}` field.");
		}
		return $r;
	}

	/**
	 * 2016-07-31
	 * @see \Df\Config\Backend::fc()
	 * @used-by \Df\Config\Backend\Serialized::processA()
	 */
	final protected function isSaving():bool {return isset($this->_data['field_config']);}

	/**
	 * 2015-12-07
	 * 2016-01-01
	 * Magento 2 (unlike Magento 1) allows configurations paths with more than 3 segments (nesting levels), e.g.:
	 * section / group / group / field.
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Cron/etc/adminhtml/system.xml#L14
	 * In Magento 1, configurations paths always contain 3 segments: section / group / field
	 * @used-by \Df\Config\Backend\Serialized::valueSerialize()
	 * @return array(string => mixed)
	 */
	final protected function value():array {return dfc($this, function() {
		# 2020-02-02
		# This code supports a custom `config_path` for a field.
		# "Magento\Config\Model\Config\Structure\AbstractElement::getPath() ignores a custom `config_path` value":
		# https://mage2.pro/t/5148
		$c = $this['field_config']; /** @var array(string => string|mixed) $c */
		# 2016-09-02
		# If the value is being saved in a non-default scope,
		# then the value's configration path in the `$this->_data` array contains the `inherit` key.
		# I delete it.
		return dfa_unset(
			dfa_deep($this->_data, df_cc_path(
				'groups', implode('/groups/', array_slice(df_explode_xpath($c['path']), 1)), 'fields', $c['id']
			))
			,'inherit'
		);
	});}

	/**
	 * 2016-08-03
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\Fieldset::beforeAddField()
	 * @param string $p
	 */
	final static function processed($p):bool {return isset(self::$_processed[$p]);}

	/**
	 * 2016-08-03
	 * @used-by self::_afterLoad()
	 * @used-by self::processed()
	 * @var array(string => bool)
	 */
	private static $_processed = [];
}