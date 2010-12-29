<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Stylesheet;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\StylesheetOptimizer;
use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Stylesheet\Minify\CSS;

/**
 * Optimize stylesheet file using the CSS_Minify library
 *
 * @author dstendardi <david.stendardi@adenclassifieds.com>
 * @author henrikbjorn
 */
class MinifyOptimizer extends StylesheetOptimizer
{
    /**
     * A cache for symlinks
     *
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
