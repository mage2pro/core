<?php
namespace Df\Framework\Console;
use Exception as E;
use Magento\Framework\Console\Cli;
use Magento\Framework\ObjectManager\NoninterceptableInterface as INonInterceptable;
use Symfony\Component\Console\Command\Command as _P;
use Symfony\Component\Console\Input\InputInterface as I;
use Symfony\Component\Console\Output\OutputInterface as O;
/**
 * 2020-10-25
 * @see \TFC\Image\Command\C1
 * @see \TFC\Image\Command\C2
 * @see \TFC\Image\Command\C3
 */
abstract class Command extends _P implements INonInterceptable {
	/**
	 * 2020-10-25
	 * @used-by self::execute()
	 * @see \TFC\GoogleShopping\Command\C1::p()
	 * @see \TFC\Image\Command\C1::p()
	 * @see \TFC\Image\Command\C2::p()
	 * @see \TFC\Image\Command\C3::p()
	 */
	abstract protected function p():void;

	/**
	 * 2020-10-25
	 * @override
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	final protected function execute(I $i, O $o):int {$this->_i = $i; $this->_o = $o; return df_try(
		function() {$this->p(); return Cli::RETURN_SUCCESS;}
		,function(E $e) use($o) {
			$o->writeln(df_tag('error', [], $e->getMessage()));
			if (O::VERBOSITY_VERBOSE <= $o->getVerbosity()) {
				$o->writeln($e->getTraceAsString());
			}
			return Cli::RETURN_FAILURE;
		}
	);}

	/**
	 * 2020-10-25
	 * 2022-10-31 @deprecated It is unused.
	 */
	final protected function input():I {return $this->_i;}

	/**
	 * 2020-10-25
	 * @used-by \TFC\Image\Command\C1::image()
	 */
	final protected function output():O {return $this->_o;}

	/**
	 * 2020-20-25
	 * @used-by self::execute()
	 * @used-by self::input()
	 * @var I
	 */
	private $_i;

	/**
	 * 2020-10-25
	 * @used-by self::execute()
	 * @used-by self::output()
	 * @var O
	 */
	private $_o;
}