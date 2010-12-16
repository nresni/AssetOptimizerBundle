<?php
namespace Bundle\AssetOptimizerBundle\Asset\Optimizer;

/**
 *
 * Enter description here ...
 * @author dstendardi
 */
use Bundle\AssetOptimizerBundle\Asset\Optimizer;
use Bundle\AssetOptimizerBundle\Asset\Optimizer\Minify\CSS;

class MinifyOptimizer extends Optimizer
{
  /**
   * (non-PHPdoc)
   * @see acAssetOptimizer::compress()
   */
  public function compress($filePath)
  {
    $source = file_get_contents($filePath);

    $directory = dirname($filePath);

    return CSS::minify($source, array('currentDir' => $directory));
  }
}