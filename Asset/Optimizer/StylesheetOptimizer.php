<?php
namespace Bundle\AssetOptimizerBundle\Asset\Optimizer;

use Bundle\AssetOptimizerBundle\Asset\Optimizer;

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