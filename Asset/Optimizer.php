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
     * @var string the file name
     */
    protected $fileMask;

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