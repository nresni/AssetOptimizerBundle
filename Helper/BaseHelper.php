<?php
namespace Bundle\AssetOptimizerBundle\Helper;
/**
 * resourcesHelper is a helper that manages resources.
 *
 * Usage:
 *
 * <code>
 *   $view['resources']->add('foo.js');
 *   echo $view['resources'];
 * </code>
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
use Bundle\AssetOptimizerBundle\Asset\Optimizer;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;

abstract class BaseHelper extends Helper
{
    /**
     *
     * Enter description here ...
     * @var unknown_type
     */
    protected $resources = array();

    /**
     *
     * Enter description here ...
     * @var unknown_type
     */
    protected $assetHelper;

    /**
     * @var Optimizer
     */
    protected $optimizer;
    
    
    /**
     * Constructor.
     *
     * @param AssetsHelper $assetHelper A AssetsHelper instance
     */
    public function __construct(AssetsHelper $assetHelper)
    {
        $this->assetHelper = $assetHelper;
    }

    /**
     * Render the html tag
     *
     * @return String html tag
     */
    abstract protected function renderTag($path, $atts);

    /**
     *
     * @return Optimizer get the optimizer
     */
    public function getOptimizer()
    {
      return $this->optimizer;
    }

    /**
     * Set the optimizer
     *
     * @param Optimizer
     */
    public function setOptimizer(Optimizer $optimizer)
    {
      $this->optimizer = $optimizer;
    }

    /**
     * Adds a Resource file.
     *
     * @param string $resource A Resource file path
     * @param array  $attributes An array of attributes
     */
    public function add($resource, $attributes = array())
    {
        $this->resources[$resource] = $attributes;
    }

    /**
     * Returns all JavaScript files.
     *
     * @return array An array of JavaScript files to include
     */
    public function get()
    {
        return $this->resources;
    }

    /**
     * Returns HTML representation of the links to resources.
     *
     * @return string The HTML representation of the resources
     */
    public function render()
    {
        if ($this->optimizer)
        {
          $this->optimizer->optimize($this);
        }

        $html = '';
        foreach ($this->resources as $path => $attributes) {
          $path = $this->assetHelper->getUrl($path);
          $atts = '';
            foreach ($attributes as $key => $value) {
                $atts .= ' '.sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, $this->charset));
            }

            $html .= $this->renderTag($path, $atts)."\n";
        }

        return $html;
    }

    /**
     * Outputs HTML representation of the links to resources.
     *
     */
    public function output()
    {
        echo $this->render();
    }

    /**
     * Returns a string representation of this helper as HTML.
     *
     * @return string The HTML representation of the resources
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     *
     * Enter description here ...
     */
    public function flush()
    {
      $this->resources = array();
    }
}