<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset;

require_once 'vfsStream/vfsStream.php';

use Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer;
use Bundle\Adenclassifieds\AssetOptimizerBundle\Helper\BaseHelper;
use vfsStreamWrapper;

/**
 *
 * Enter description here ...
 * @author dstendardi
 */
class OptimizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Initialize vfs
     */
    protected function setUp()
    {
         vfsStreamWrapper::register();

         mkdir('vfs://tmp/cache', 0777, true);

         $this->helper = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Helper\BaseHelper')->disableOriginalConstructor()->disableOriginalClone()->setMethods(array('renderTag', 'getName', 'getLocalResources', 'add', 'remove', 'get'))->getMock();
    }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::optimize
     */
    public function testOptimize()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer')->disableOriginalConstructor()->setMethods(array('process', 'compress', 'collect', 'getFileName', 'getCachePath', 'getAssetPath', 'filterResources'))->getMock();

        $resources = array('/foo.css' => array(), '/bar.css' => array());

        $this->optimizer->expects($this->any())->method('collect')->with($this->helper)->will($this->returnValue($resources));

        $this->optimizer->expects($this->once())->method('process')->with($resources);

        $this->helper->expects($this->once())->method('add')->with($this->equalTo("/cache/foo-bar.css"));

        $this->helper->expects($this->at(0))->method('remove')->with($this->equalTo('/foo.css'));

        $this->helper->expects($this->at(1))->method('remove')->with($this->equalTo('/bar.css'));

        $this->optimizer->expects($this->any())->method('getAssetPath')->will($this->returnValue('vfs://tmp'));

        $this->optimizer->expects($this->any())->method('getCachePath')->will($this->returnValue('vfs://tmp/cache'));

        $this->optimizer->expects($this->any())->method('getFileName')->will($this->returnValue('foo-bar.css'));

        $this->optimizer->optimize($this->helper);
    }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::doOptimize
     */
     public function testProcess()
     {
          file_put_contents('vfs://tmp/foo.css', 'a');

          file_put_contents('vfs://tmp/bar.css', 'b');

          $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('filterResources', 'getAssetPath', 'compress'))->disableOriginalConstructor()->getMock();

          $this->optimizer->expects($this->any())->method('getAssetPath')->will($this->returnValue('vfs://tmp'));

          $this->optimizer->expects($this->any())->method('filterResources')->will($this->returnArgument(0));

          $this->optimizer->expects($this->any())->method('compress')->will($this->onConsecutiveCalls('a', 'b'));

          $this->assertEquals('ab', $this->optimizer->exposeProcess(array('/foo.css' => array(), '/bar.css' => array())));
     }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::doOptimize
     * @expectedException InvalidArgumentException
     */
     public function testDoOptimizeThrowAnExceptionWhenAFileIsNotFound()
     {
          file_put_contents('vfs://tmp/foo.css', 'a');

          $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('filterResources', 'getAssetPath', 'compress'))->disableOriginalConstructor()->getMock();

          $this->optimizer->expects($this->any())->method('getAssetPath')->will($this->returnValue('vfs://tmp'));

          $this->optimizer->expects($this->any())->method('filterResources')->will($this->returnArgument(0));

          $this->optimizer->expects($this->any())->method('compress')->will($this->onConsecutiveCalls('a', 'b'));

          $this->assertEquals('ab', $this->optimizer->exposeProcess(array('/foo.css' => array(), '/bar.css' => array())));
     }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::collect
     */
    public function testCollect()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer')->disableOriginalConstructor()->setMethods(array('compress'))->getMock();

        $resources = array('vfs://tmp/foo.css' => array(), 'http://tmp/bar.css' => array());

        $this->helper->expects($this->once())->method('get')->will($this->returnValue($resources));

        $this->assertEquals(array('vfs://tmp/foo.css' => array()), $this->optimizer->collect($this->helper));
    }

    /**
     * @test
     * @cover Bundle\AssetOptmizerBundle\Asset\Optimizer::getFileName
     */
    public function testGetFileName()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->expects($this->any())->method('getRequestUserAgent')->will($this->returnValue('foo'));

        $this->optimizer->expects($this->any())->method('getFileMask')->will($this->returnValue('mask-<signature>.css'));

        $this->assertEquals('mask-cdda47aa3f963c11d4850a9e7b21353b.css', $this->optimizer->exposeGetFileName(array('vfs://tmp/foo.css' => array(), 'vfs://tmp/bar.css' => array())), 'the file name contains the expected md5 hash');
    }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::getFileName
     */
    public function testGetFileNameDoesNotDependsOnResourcesOrder()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->expects($this->any())->method('getFileMask')->will($this->returnValue('mask-<signature>.css'));

        $this->optimizer->expects($this->any())->method('getRequestUserAgent')->will($this->returnValue('foo'));

        $this->assertEquals('mask-cdda47aa3f963c11d4850a9e7b21353b.css', $this->optimizer->exposeGetFileName(array('vfs://tmp/bar.css' => array(), 'vfs://tmp/foo.css' => array())), 'the md5 hash does not depends on resources order');
    }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::getFileName
     */
    public function testGetFileNameDependsOnRequestUserAgent()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->expects($this->any())->method('getFileMask')->will($this->returnValue('mask-<signature>.css'));

        $this->optimizer->expects($this->any())->method('getRequestUserAgent')->will($this->returnValue('bar'));

        $this->assertEquals('mask-a499e21d88613b36c559c633a7376017.css', $this->optimizer->exposeGetFileName(array('vfs://tmp/bar.css' => array(), 'vfs://tmp/foo.css' => array())), 'the md5 hash does depends on user agent');
    }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::setAssetPath
     * @expectedException InvalidArgumentException
     */
    public function testSetCachePathThrowAnExceptionWhenPathDoesNotExists()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->setAssetPath('foo');
    }

    /**
     * @test
     * @cover Bundle\Adenclassifieds\AssetOptimizerBundle\Asset\Optimizer::setCachePath
     * @expectedException InvalidArgumentException
     */
    public function testSetAssetPathThrowAnExceptionWhenPathDoesNotExists()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->setCachePath('foo');
    }
}