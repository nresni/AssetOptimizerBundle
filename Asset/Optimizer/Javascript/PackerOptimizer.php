<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Javascript;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\JavascriptOptimizer;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer\Javascript\Packer\Packer;

/**
 *
 * Enter description here ...
 * @author dstendardi
 *
 */
class PackerOptimizer extends JavascriptOptimizer
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