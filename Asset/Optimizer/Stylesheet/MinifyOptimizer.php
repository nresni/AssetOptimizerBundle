<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Stylesheet;


use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\StylesheetOptimizer;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Stylesheet\Minify\CSS;

class MinifyOptimizer extends StylesheetOptimizer
{
  /**
   * (non-PHPdoc)
   * @see acAssetOptimizer::compress()
   */
  public function compress($filePath)
  {
    $source = file_get_contents($filePath);

    $directory = dirname($filePath);

    return CSS::minify($source, array('currentDir' => $directory, 'docRoot'=> $this->getAssetPath()));
  }
}
