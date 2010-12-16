<?php
namespace Bundle\AssetOptimizerBundle\Helper;

use Bundle\AssetOptimizerBundle\Helper\ResourceCollectionHelper;
/**
 *
 * Enter description here ...
 * @author dstendardi
 */
class JavascriptsHelper extends BaseHelper
{
    /**
     * (non-PHPdoc)
     * @see Bundle\AssetOptimizerBundle\Helper.ResourceCollectionHelper::renderTag()
     */
    protected function renderTag($path, $atts)
    {
      return sprintf('<script type="text/javascript" src="%s"%s></script>', $path, $atts);
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