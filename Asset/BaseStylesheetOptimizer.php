<?php
namespace Bundle\AssetOptimizerBundle\Asset;

use Bundle\AssetOptimizerBundle\Asset\Optimizer;

/**
 * 
 * Enter description here ...
 * @author dstendardi
 *
 */
abstract class BaseStylesheetOptimizer extends Optimizer
{
    protected $fileName = 'compressed-<signature>.css';
}