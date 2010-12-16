<?php
namespace Bundle\AssetOptimizerBundle\Asset\Optimizer;

use Bundle\AssetOptimizerBundle\Asset\Optimizer;

use Bundle\AssetOptimizerBundle\Asset\Optimizer\Packer\Packer;

/**
 *
 * Enter description here ...
 * @author dstendardi
 *
 */
class PackerOptimizer extends Optimizer
{
  /**
   * (non-PHPdoc)
   * @see acAssetOptimizer::compress()
   */
  public function compress($filePath)
  {
    $source = file_get_contents($filePath);

    $source = iconv('UTF-8', 'CP1252', $source);

    $packer = new Packer($source, 'None', true, false);

    $compressed = $packer->pack();

    $compressed = iconv('CP1252', 'UTF-8', $compressed);

    return $compressed;
  }
}