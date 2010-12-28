<?php

namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Finder\Finder;

/**
 * AssetsClearCacheCommand.
 *
 * @author David Stendardi <david.stendardi@gmail.com>
 */
class AssetsClearCacheCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('assets:optimizer:clear-cache');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cachePath = $this->container->getParameter('assetoptimizer.cache_path');
        $filesystem = new Filesystem();

        // Remove and Recreaetd the cachepath
        $filesystem->remove($cachePath);

        if ($filesystem->mkdirs($cachePath)) {
            $filesystem->chmod($cachePath, 0777);
        } else {
            throw new \LogicException(sprintf('Could not create the directory "%s"', $cachePath));
        }

        $output->writeln(sprintf('Removed cache files in <info>%s</info>.', realpath($cachePath)));
    }
}
