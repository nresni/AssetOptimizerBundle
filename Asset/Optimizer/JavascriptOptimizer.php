<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

/**
 *
 * Enter description here ...
 * @author dstendardi
 *
 */
abstract class JavascriptOptimizer extends Optimizer
{
    protected $fileMask = 'compressed-<signature>.js';
}