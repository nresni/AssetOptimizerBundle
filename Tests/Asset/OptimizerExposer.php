<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Templating\Helper\BaseHelper;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;

/**
 * Exposer for the get file name method
 *
 * @author dstendardi
 */
class OptimizerExposer extends Optimizer
{
    /**
     * (non-PHPdoc)
     * @see src/Bundle/AssetOptimizerBundle/Asset/Bundle\Adenclassifieds\AssetOptimizerBundle\Asset.Optimizer::compress()
     */
    protected function compress($filepath)
    {
        //;
    }

    /**
     * Exposes the protected getFileName method
     *
     * @param array resources
     */
    public function exposeGetFileName(array $resources)
    {
        return $this->getFileName($resources);
    }

    /**
     * Exposes the protected process name method
     *
     * @param array resources
     */
    public function exposeProcess(array $resources)
    {
        return $this->process($resources);
    }

    /**
     * Exposes the protected collect name method
     *
     * @param BaseHelper helper
     */
    public function exposeCollect(BaseHelper $helper)
    {
        return $this->collect($helper);
    }
}