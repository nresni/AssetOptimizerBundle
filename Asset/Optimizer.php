<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Asset;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Bundle\FrameworkBundle\EventDispatcher;
use Bundle\Adenclassifieds\AssetOptimizerBundle\Templating\Helper\BaseHelper;

/**
 * Base optimizer class.
 * Provides the cache system and calls the parent "compress"
 * method in order to optimize the assets
 *
 * @author dstendardi <david.stendardi@adenclassifieds.com>
 * @author henrikbjorn
 */
abstract class Optimizer
{
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
     * @var Boolean
     */
    protected $debug = false;

    /**
     * Constructor.
     *
     * @param acHelperAsset $assetHelper A acHelperAsset instance
     */
    public function __construct(EventDispatcher $eventDispatcher, $assetPath, $cachePath, $debug = false)
    {
        $this->setEventDispatcher($eventDispatcher);

        $this->setAssetPath($assetPath);

        $this->setCachePath($cachePath);

        $this->debug = (Boolean) $debug;
    }

    /**
     * Gets and compresses code from the given file path
     *
     * @param string $filePath
     */
    protected abstract function compress($filePath);

    /**
     * Collect and compress asset code inside a unique file
     * using basePath & fileName configuration
     *
     * @param BaseHelper resource collection
     */
    public function optimize(BaseHelper $helper)
    {
        $resources = $this->collect($helper);

        // If no resources are found or they all are external or standalone done proceed
        if (false === (Boolean) count($resources)) {
            return;
        }

        $name = $this->getFileName($resources);

        $filePath = $this->getCachePath().'/'.$name;

        if (true === $this->debug && file_exists($filePath)) {
            unlink($filePath);
        }

        if ( ! file_exists($filePath)) {
            $code = $this->process($resources);
            if (false === file_put_contents($filePath, $code)) {
                throw new \RuntimeException("Unable to write the file <$filePath>");
            }
        }

        foreach ($resources as $resource => $attributes) {
            $helper->remove($resource);
        }

        $directory = str_replace($this->getAssetPath().'/', '', $this->getCachePath());

        $helper->add($directory.'/'.$name);
    }

    /**
     * Create the cache file
     *
     * @param array resources
     * @param string file path
     */
    protected function process(array $resources)
    {
        $buffer = '';

        $resources = $this->filterResources($resources);

        foreach ($resources as $resource => $attributes) {

            $path = $this->getAssetPath().'/'.$resource;

            if (  ! file_exists($path)) {
                throw new \InvalidArgumentException('The following file does not exists : '.$path);
            }

            $buffer .= $this->compress($path);
        }

        return $buffer;
    }

    /**
     * Collect the optimizible resources
     *
     * @param BaseHelper helper
     * @return array resources
     */
    public function collect(BaseHelper $helper)
    {
        $locals = array();
        foreach ($helper->get() as $uri => $attributes) {
            if (isset($attributes['standalone'])) {
                $standalone = (Boolean) $attributes['standalone'];

                unset($attributes['standalone']);

                if (true === $standalone) {
                    continue;
                }
            }

            // If a scheme is returned or if the url starts with // lets assume its a external resource
            if (in_array(parse_url($uri, PHP_URL_SCHEME), array('http', 'https')) || '//' == substr($uri, 0, 2)) {
               continue; 
            }

            $locals[$uri] = $attributes;
        }
        return $locals;
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
     * Returns the expected file name for the bundled file.
     * The signature is deduced from the user agent & the file names
     *
     * @return string file name
     */
    protected function getFileName(array $resources)
    {
        asort($resources);

        $signature = implode('-', array_keys($resources));

        $name = strtr($this->getFileMask(), array('<signature>' => md5($signature)));

        return $name;
    }

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

        if (false === $this->assetPath) {
            throw new \InvalidArgumentException("The given asset path does not exists : $path");
        }
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

        if (false === $this->cachePath) {
            throw new \InvalidArgumentException("The given cache path does not exists : $path");
        }
    }

    /**
     * @return string cache path
     */
    public function getCachePath()
    {
        return $this->cachePath;
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
}
