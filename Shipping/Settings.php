<?php
namespace Df\Shipping;
use Df\Config\Source as ConfigSource;
use Df\Core\Exception as DFE;
use Df\Shipping\Method as M;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Payment\Model\Checks\TotalMinMax as T;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;
/**
 * 2018-04-21
 * @see \Frugue\Shipping\Settings
 * @see \Doormall\Shipping\Settings
 */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2018-04-21
	 * @override
	 * @see \Df\Config\Settings::__construct()
	 * @used-by \Df\Shipping\Method::s()
	 */
	final function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2018-04-21
	 * @override
	 * @see \Df\Config\Settings::enable()
	 * @used-by \Df\Shipping\ConfigProvider::getConfig()
	 * @param null|string|int|S $s [optional]
	 */
	final function enable($s = null):bool {return df_bool(df_cfg(['carriers', dfsm_code($this), 'active'], $s));}

	/**
	 * 2016-08-25
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @see \Frugue\Shipping\Settings::prefix()
	 * @see \Doormall\Shipping\Settings::prefix()
	 */
	protected function prefix():string {return dfc($this, function() {return 'df_shipping/' . dfsm_code_short($this->_m);});}

	/**
	 * 2018-04-21
	 * @used-by self::__construct()
	 * @used-by self::m()
	 * @used-by self::prefix()
	 * @used-by self::scopeDefault()
	 * @var M
	 */
	private $_m;
}