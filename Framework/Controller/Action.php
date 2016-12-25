<?php
// 2016-12-25
namespace Df\Framework\Controller;
use Magento\Framework\App\Action\Action as Sb;
use Magento\Framework\App\Action\Context;
abstract class Action extends Sb {
	/**
	 * 2016-12-25
	 * @param Sb|null $sb [optional]
	 */
	public function __construct(Sb $sb = null) {
		$this->_sb = $sb ?: $this;
		parent::__construct(df_o(Context::class));
	}

	/**
	 * 2016-12-25
	 * @param string $path
	 * @param array $args
	 * @return $this
	 */
	protected function redirect($path, $args = []) {return $this->sb()->_redirect($path, $args);}

	/**
	 * 2016-12-25
	 * @return Sb
	 */
	protected function sb() {return $this->_sb;}

	/**
	 * 2016-12-25
	 * @var Sb
	 */
	private $_sb;
}