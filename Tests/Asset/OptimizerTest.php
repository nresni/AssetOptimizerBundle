<?php
namespace Bundle\AssetOptimizerBundle\Tests\Asset;

require_once 'vfsStream/vfsStream.php';

use Bundle\AssetOptimizerBundle\Asset\Optimizer;
use Bundle\AssetOptimizerBundle\Helper\BaseHelper;
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

         $this->helper = $this->getMockBuilder('Bundle\AssetOptimizerBundle\Helper\BaseHelper')->disableOriginalConstructor()->disableOriginalClone()->setMethods(array('renderTag', 'getName', 'get', 'add'))->getMock();
    }

    /**
     * @test
     * @cover Bundle\AssetOptimizerBundle\Asset\Optimizer::optimize
     */
    public function testOptimize()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\AssetOptimizerBundle\Asset\Optimizer')->disableOriginalConstructor()->setMethods(array('compress', 'getFileName', 'getCachePath', 'getAssetPath'))->getMock();

        $this->helper->expects($this->any())->method('get')->will($this->returnValue(array('vfs://tmp/foo.css' => array(), 'vfs://tmp/bar.css' => array())));

        $this->helper->expects($this->once())->method('add')->with($this->equalTo("/cache/foo-bar.css"));

        $this->optimizer->expects($this->any())->method('compress')->will($this->onConsecutiveCalls('a', 'b'));

        $this->optimizer->expects($this->any())->method('getAssetPath')->will($this->returnValue('vfs://tmp'));

        $this->optimizer->expects($this->any())->method('getCachePath')->will($this->returnValue('vfs://tmp/cache'));

        $this->optimizer->expects($this->any())->method('getFileName')->will($this->returnValue('foo-bar.css'));

        $this->optimizer->optimize($this->helper);
    }

    /**
     * @test
     * @cover Bundle\AssetOptmizerBundle\Asset\Optimizer::getFileName
     */
    public function testGetFileName()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->expects($this->any())->method('getRequestUserAgent')->will($this->returnValue('foo'));

        $this->optimizer->expects($this->any())->method('getFileMask')->will($this->returnValue('mask-<signature>.css'));

        $this->helper->expects($this->any())->method('get')->will($this->returnValue(array('vfs://tmp/foo.css' => array(), 'vfs://tmp/bar.css' => array())));

        $this->assertEquals('mask-cdda47aa3f963c11d4850a9e7b21353b.css', $this->optimizer->exposeGetFileName($this->helper), 'the file name contains the expected md5 hash');
    }

    /**
     * @test
     * @cover Bundle\AssetOptimizerBundle\Asset\Optimizer::getFileName
     */
    public function testGetFileNameDoesNotDependsOnResourcesOrder()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->expects($this->any())->method('getFileMask')->will($this->returnValue('mask-<signature>.css'));

        $this->helper->expects($this->any())->method('get')->will($this->returnValue(array('vfs://tmp/bar.css' => array(), 'vfs://tmp/foo.css' => array())));

        $this->optimizer->expects($this->any())->method('getRequestUserAgent')->will($this->returnValue('foo'));

        $this->assertEquals('mask-cdda47aa3f963c11d4850a9e7b21353b.css', $this->optimizer->exposeGetFileName($this->helper), 'the md5 hash does not depends on resources order');
    }

    /**
     * @test
     * @cover Bundle\AssetOptimizerBundle\Asset\Optimizer::getFileName
     */
    public function testGetFileNameDependsOnRequestUserAgent()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\AssetOptimizerBundle\Tests\Asset\OptimizerExposer')->setMethods(array('getRequestUserAgent', 'getFileMask'))->disableOriginalConstructor()->getMock();

        $this->optimizer->expects($this->any())->method('getFileMask')->will($this->returnValue('mask-<signature>.css'));

        $this->helper->expects($this->any())->method('get')->will($this->returnValue(array('vfs://tmp/bar.css' => array(), 'vfs://tmp/foo.css' => array())));

        $this->optimizer->expects($this->any())->method('getRequestUserAgent')->will($this->returnValue('bar'));

        $this->assertEquals('mask-a499e21d88613b36c559c633a7376017.css', $this->optimizer->exposeGetFileName($this->helper), 'the md5 hash does depends on user agent');
    }
}