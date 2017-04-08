<?php
namespace Df\Framework\Composer;
/**
 * 2016-07-05
 * Класс @see \Magento\Framework\Composer\ComposerFactory
 * отсутствует в версиях Magento ранее 2.1.0:
 * https://github.com/magento/magento2/tree/2.0.7/lib/internal/Magento/Framework/Composer
 * https://github.com/magento/magento2/tree/2.1.0/lib/internal/Magento/Framework/Composer
 * https://mail.google.com/mail/u/0/#inbox/155b9d99a00e3df5
 * Поэтому дублируем его у себя.
 */
/**
 * 2016-07-01
 * В ядре имеется отличный класс @see \Magento\Framework\Composer\ComposerInformation,
 * однако мы не имеем возможности получить доступ к его приватному методу
 * @see \Magento\Framework\Composer\ComposerInformation::getLocker()
 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/Composer/ComposerInformation.php#L367-L378
 * Если бы этот метод имел хотя бы область видимости protected,
 * то мы бы с удовольствием перекрыли его через preference
 * и повысили бы область видимости метода,
 * повторно используя тем самым объекты ядра
 * @see \Magento\Framework\Composer\ComposerInformation::$locker
 * или @see \Magento\Framework\Composer\ComposerInformation::$composer,
 * а не создавая свои вместо них.
 * Однако с областью видимости private это невозможно...
 */
class ComposerInformation {
	/**
	 * 2016-07-01
	 * @return \Composer\Package\Locker
	 */
	function locker() {return $this->composer()->getLocker();}

	/**
	 * 2016-07-01
	 * @return \Composer\Composer
	 */
	private function composer() {
		if (!isset($this->{__METHOD__})) {
			/** @var ComposerFactory $factory */
			$factory = df_o(ComposerFactory::class);
			$this->{__METHOD__} = $factory->create();
		}
		return $this->{__METHOD__};
	}
}

