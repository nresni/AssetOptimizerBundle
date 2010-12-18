<?php
namespace Bundle\AssetOptimizerBundle\Asset;

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
    * Constructor.
    *
    * @param acHelperAsset $assetHelper A acHelperAsset instance
    */
    public function __construct(Request $request, $assetPath, $cachePath)
    {
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
    public function optimize(BaseHelper $resources)
    {
        $optimized = '';

        $name = $this->getFileName($resources);

        $filePath = $this->getCachePath().'/'.$name;

        if ( ! file_exists($filePath)) {

          foreach ($resources->get() as $resource => $attributes) {
                $optimized .= $this->compress($this->assetPath.$resource);
          }

          if (false === file_put_contents($filePath, $optimized)) {
                throw new \RuntimeException("Unable to write the file <$filePath>");
          }
        }

        $resources->flush();

        $directory = str_replace($this->getAssetPath(), '', $this->getCachePath());

        $resources->add($directory.'/'.$name);
    }

   /**
    * @param string user agent
    */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

   /**
    * @param string $fileName
    */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

   /**
    * example : cache/foo-1-gecko.css
    *
    * @return string file name
    */
    protected function getFileName(BaseHelper $resources)
    {
        $signature = array_keys($resources->get());

        $signature[] = $this->request->headers->get('User-Agent');

        $signature = implode('-', $signature);

        $name = strtr($this->fileName, array('<signature>' => md5($signature)));

        return $name;
    }
}