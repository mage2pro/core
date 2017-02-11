<?php
/**
 * 2016-07-05
 * Класс @see \Magento\Framework\Composer\ComposerFactory
 * отсутствует в версиях Magento ранее 2.1.0:
 * https://github.com/magento/magento2/tree/2.0.7/lib/internal/Magento/Framework/Composer
 * https://github.com/magento/magento2/tree/2.1.0/lib/internal/Magento/Framework/Composer
 * https://mail.google.com/mail/u/0/#inbox/155b9d99a00e3df5
 * Поэтому дублируем его у себя.
 */
namespace Df\Framework\Composer;
use Composer\IO\BufferIO;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Composer\ComposerJsonFinder;
class ComposerFactory
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ComposerJsonFinder
     */
    private $composerJsonFinder;

    /**
     * @param DirectoryList $directoryList
     * @param ComposerJsonFinder $composerJsonFinder
     */
    function __construct(
        DirectoryList $directoryList,
        ComposerJsonFinder $composerJsonFinder
    ) {
        $this->directoryList = $directoryList;
        $this->composerJsonFinder = $composerJsonFinder;
    }

    /**
     * Create \Composer\Composer
     *
     * @return \Composer\Composer
     * @throws \Exception
     */
    function create()
    {
        if (!getenv('COMPOSER_HOME')) {
            putenv('COMPOSER_HOME=' . $this->directoryList->getPath(DirectoryList::COMPOSER_HOME));
        }
        return \Composer\Factory::create(
            new BufferIO(),
            $this->composerJsonFinder->findComposerJson()
        );
    }
}
