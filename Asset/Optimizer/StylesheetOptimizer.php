<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

/**
 *
 * Enter description here ...
 * @author dstendardi
 *
 */
abstract class StylesheetOptimizer extends Optimizer
{
    protected $fileMask = 'compressed-<signature>.css';
}