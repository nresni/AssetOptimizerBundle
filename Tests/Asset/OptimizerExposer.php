<?php
namespace Bundle\AssetOptimizerBundle\Tests\Asset;

use Bundle\AssetOptimizerBundle\Helper\BaseHelper;

use Bundle\AssetOptimizerBundle\Asset\Optimizer;

/**
 * Exposer for the get file name method
 *
 * @author dstendardi
 */
class OptimizerExposer extends Optimizer
{
    /**
     * (non-PHPdoc)
     * @see src/Bundle/AssetOptimizerBundle/Asset/Bundle\AssetOptimizerBundle\Asset.Optimizer::compress()
     */
    protected function compress($filepath)
    {
        //;
    }

    /**
     * Exposes the protected get file name method
     *
     * @param BaseHelper helper
     */
    public function exposeGetFileName(BaseHelper $helper)
    {
        return $this->getFileName($helper);
    }
}