<?php
namespace Bundle\AssetOptimizerBundle\Helper;

use Bundle\AssetOptimizerBundle\Helper\ResourceCollectionHelper;
/**
 *
 * Enter description here ...
 * @author dstendardi
 */
class StylesheetsHelper extends BaseHelper
{
    /**
     * (non-PHPdoc)
     * @see Bundle\AssetOptimizerBundle\Helper.ResourceCollectionHelper::renderTag()
     */
    protected function renderTag($path, $atts)
    {
      return sprintf('<link href="%s" rel="stylesheet" type="text/css"%s />', $path, $atts);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'stylesheets';
    }
}