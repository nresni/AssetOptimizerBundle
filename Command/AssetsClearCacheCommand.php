<?php

namespace Bundle\AssetOptimizerBundle\Command;

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
        $filesystem = new Filesystem();

        $finder = new Finder();

        $finder = $finder->files()->in($this->container->getParameter('assetoptimizer.cache_path'));

        $count = 0;

        foreach($finder as $file) {
          $filesystem->remove($file);
          $count++;
        }

        $output->writeln("removed $count cache file(s)");
    }
}
