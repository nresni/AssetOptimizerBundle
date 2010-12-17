<?php
namespace Bundle\AssetOptimizerBundle\Asset;

use Symfony\Component\HttpFoundation\Request;

use Bundle\AssetOptimizerBundle\Helper\BaseHelper;

use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
/**
 *
 * Enter description here ...
 * @author dstendardi
 *
 */
abstract class Optimizer
{
  /**
   * @var Request instance
   */
  protected $request;

  /**
   *@var string path for assets
   */
  protected $assetPath;

  /**
   * 
   * Enter description here ...
   * @var unknown_type
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
   * @var string full path to asset directory
   */
  public function setAssetPath($path)
  {
      $this->assetPath = realpath($path);    
  }
  
  /**
   * @var string full path to cache directory
   */
  public function setCachePath($path)
  {
      $this->cachePath = realpath($path);    
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
   */
  public function optimize(BaseHelper $tagCollection)
  {
    $optimized = '';

    $name = $this->getFileName();

    $filePath = $this->cachePath.'/'.$name;

    if ( ! file_exists($filePath)) {

      foreach ($tagCollection->get() as $resource => $attributes)
      {          
        $optimized .= $this->compress($this->assetPath.$resource);
      }

      if ( ! file_put_contents($filePath, $optimized))
      {
        throw new RuntimeException("Unable to write the file <$filePath>");
      }
    }

    $tagCollection->flush();

    $directory = str_replace($this->assetPath, '', $this->cachePath);
    
    $tagCollection->add($directory.'/'.$name);
  }

  /**
   * @param string user agent
   */
  public function setRequest(Request $request)
  {
    $this->request = $request;
  }

  /**
   * <code>
   *   $helper->setFileName('cache/foo-<userAgent>.css');
   *
   *   echo $helper // <link href="http://assets.foo.fr/cache/foo-gecko.css" rel="stylesheet" type="text/css" />
   * </code>
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
  protected function getFileName()
  {
    $name = strtr($this->fileName, array('<signature>' => md5($this->request->headers->get('User-Agent'))));

    return $name;
  }
}