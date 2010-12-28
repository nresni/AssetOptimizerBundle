<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Stylesheet;


use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\StylesheetOptimizer;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Stylesheet\Minify\CSS;

class MinifyOptimizer extends StylesheetOptimizer
{
    /**
     * @var array
     */
    static $symlinks = array();

    /**
     * (non-PHPdoc)
     * @see acAssetOptimizer::compress()
     */
    public function compress($filePath)
    {
        $source = file_get_contents($filePath);

        $directory = dirname($filePath);

        $realDirectory = dirname(realpath($filePath));

        $symlinks = array();

        if (false === in_array($directory, self::$symlinks) && $realDirectory !== $directory) {
            self::$symlinks[$directory] = $realDirectory;
        }

        return CSS::minify($source, array(
            'currentDir' => $directory,
            'docRoot'    => $this->getAssetPath(),
            'symlinks'   => self::$symlinks,
        ));
    }
}
