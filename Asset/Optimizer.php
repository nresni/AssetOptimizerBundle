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
   * @var AssetsHelper instnace
   */
  protected $assetHelper;

  /**
   * @var Request instance
   */
  protected $request;

  /**
   *@var string path for assets
   */
  protected $path;


  /**
   * Constructor.
   *
   * @param acHelperAsset $assetHelper A acHelperAsset instance
   */
  public function __construct(Request $request, AssetsHelper $assetHelper, $path)
  {
    $this->setRequest($request);

    $this->setAssetHelper($assetHelper);

    $this->setPath($path);
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

    $filePath = $this->path.'/'.$name;

    if ( ! file_exists($filePath)) {

      foreach ($tagCollection->get() as $resource => $attributes)
      {
        $optimized .= $this->compress($this->path.$resource);
      }

      if ( ! file_put_contents($filePath, $optimized))
      {
        throw new RuntimeException("Unable to write the file <$filePath>");
      }
    }

    $tagCollection->flush();

    $tagCollection->add($name);
  }

  /**
   * @return acAssetHelper assetHelper
   */
  public function getAssetHelper()
  {
    return $this->assetHelper;
  }

  /**
   * @param acAssetHelper assetHelper
   */
  public function setAssetHelper(AssetsHelper $assetHelper)
  {
    $this->assetHelper = $assetHelper;
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
   *
   */
  public function setPath($path)
  {
    $this->path = $path;
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