<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

/**
 * This is the base class for Javascript optimizers
 * A custom optimizer should extends it
 *
 * @author dstendardi <david.stendardi@adenclassifieds.com>
 */
abstract class JavascriptOptimizer extends Optimizer
{
    /**
     * The target file name, where signature is replaced with a hash
     * based on file names and user agent
     *
     * @var string file name
     */
    protected $fileMask = 'compressed-<signature>.js';
}