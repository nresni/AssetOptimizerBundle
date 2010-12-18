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
    }

    /**
     * @test
     * @cover Bundle\AssetOptimizerBundle\Asset\Optimizer::optimize
     */
    public function testOptimize()
    {
        $this->optimizer = $this->getMockBuilder('Bundle\AssetOptimizerBundle\Asset\Optimizer')->disableOriginalConstructor()->setMethods(array('compress', 'getFileName', 'getCachePath', 'getAssetPath'))->getMock();

        $helper = $this->getMockBuilder('Bundle\AssetOptimizerBundle\Helper\BaseHelper')->disableOriginalConstructor()->disableOriginalClone()->setMethods(array('renderTag', 'getName', 'get', 'add'))->getMock();

        $helper->expects($this->any())->method('get')->will($this->returnValue(array('vfs://tmp/foo.css' => array(), 'vfs://tmp/bar.css' => array())));

        $helper->expects($this->once())->method('add')->with($this->equalTo("/cache/foo-bar.css"));

        $this->optimizer->expects($this->any())->method('compress')->will($this->onConsecutiveCalls('a', 'b'));

        $this->optimizer->expects($this->any())->method('getAssetPath')->will($this->returnValue('vfs://tmp'));

        $this->optimizer->expects($this->any())->method('getCachePath')->will($this->returnValue('vfs://tmp/cache'));

        $this->optimizer->expects($this->any())->method('getFileName')->will($this->returnValue('foo-bar.css'));

        $this->optimizer->optimize($helper);
    }
}