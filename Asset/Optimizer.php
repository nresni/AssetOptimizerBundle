<?php
namespace Bundle\AssetOptimizerBundle\Asset;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Bundle\FrameworkBundle\EventDispatcher;

use Symfony\Component\HttpFoundation\Request;

use Bundle\AssetOptimizerBundle\Helper\BaseHelper;

use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;

/**
 * Enter description here ...
 * @author dstendardi
 */
abstract class Optimizer
{
   /**
    * @var Request instance
    */
    protected $request;

   /**
    *@var string path where to find assets
    */
    protected $assetPath;

   /**
    * @var string path where to write optimized files
    */
    protected $cachePath;

    /**
     * @var string the file name
     */
    protected $fileMask;

   /**
    * @var EventDispatcher dispatcher
    */
    protected $eventDispatcher;

   /**
    * Constructor.
    *
    * @param acHelperAsset $assetHelper A acHelperAsset instance
    */
    public function __construct(EventDispatcher $eventDispatcher, Request $request, $assetPath, $cachePath)
    {
        $this->setEventDispatcher($eventDispatcher);

        $this->setRequest($request);

        $this->setAssetPath($assetPath);

        $this->setCachePath($cachePath);
    }

   /**
    * Gets and compresses code from the given file path
    *
    * @param string $filePath
    */
    protected abstract function compress($filePath);

   /**
    * @param EventDispatcher dispatcher
    */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

   /**
    * @var string full path to asset directory
    */
    public function setAssetPath($path)
    {
        $this->assetPath = realpath($path);
    }

    /**
     * @return string asset path
     */
    public function getAssetPath()
    {
        return $this->assetPath;
    }

   /**
    * @var string full path to cache directory
    */
    public function setCachePath($path)
    {
        $this->cachePath = realpath($path);
    }

    /**
     * @return string cache path
     */
    public function getCachePath()
    {
        return $this->cachePath;
    }

    /**
    * Collect and compress asset code inside a unique file
    * using basePath & fileName configuration
    *
    * @param BaseHelper resource collection
    */
    public function optimize(BaseHelper $helper)
    {
        $optimized = '';

        $name = $this->getFileName($helper);

        $filePath = $this->getCachePath().'/'.$name;

        if ( ! file_exists($filePath)) {

          $resources = $helper->get();

          $resources = $this->filterResources($resources);

          foreach ($resources as $resource => $attributes) {
                $optimized .= $this->compress($this->assetPath.$resource);
          }

          if (false === file_put_contents($filePath, $optimized)) {
                throw new \RuntimeException("Unable to write the file <$filePath>");
          }
        }

        $helper->flush();

        $directory = str_replace($this->getAssetPath(), '', $this->getCachePath());

        $helper->add($directory.'/'.$name);
    }

   /**
    * Filter the resources using event dispatcher
    *
    * @param array resources
    * @return array filtered resources
    */
    public function filterResources(array $resources)
    {
        $event = new Event($this, 'assetoptimizer.filter_resources');

        $this->getEventDispatcher()->filter($event, $resources);

        return $event->getReturnValue();
    }

   /**
    * @param Request instance
    */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request  instance
     */
    public function getRequest()
    {
        return $this->request;
    }

   /**
    * @param string file mask with replacements
    */
    public function setFileMask($fileMask)
    {
        $this->fileMask = $fileMask;
    }

    /**
     * @return string a mask with replacements
     */
    protected function getFileMask()
    {
        return $this->fileMask;
    }

    /**
     * @return string user agent
     */
    public function getRequestUserAgent()
    {
        return $this->request->headers->get('User-Agent');
    }

   /**
    * Returns the expected file name for the bundled file.
    * The signature is deduced from the user agent & the file names
    *
    * @return string file name
    */
    protected function getFileName(BaseHelper $helper)
    {
        $resources = $helper->get();

        $signature = array_keys($resources);

        sort($signature);

        $signature[] = $this->getRequestUserAgent();

        $signature = implode('-', $signature);

        $name = strtr($this->getFileMask(), array('<signature>' => md5($signature)));

        return $name;
    }
}