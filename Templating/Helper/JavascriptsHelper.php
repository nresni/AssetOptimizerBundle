<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Templating\Helper;

use Bundle\Adenclassifieds\AssetOptimizerBundle\Helper\Templating\ResourceCollectionHelper;
/**
 *
 * Enter description here ...
 * @author dstendardi
 */
class JavascriptsHelper extends BaseHelper
{
    /**
     * (non-PHPdoc)
     * @see Bundle\Adenclassifieds\AssetOptimizerBundle\Helper.ResourceCollectionHelper::renderTag()
     */
    protected function renderTag($path, $atts)
    {
      return sprintf('<script type="text/javascript" src="%s?%s"%s></script>', $path, $this->assetHelper->getVersion(), $atts);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'javascripts';
    }
}
