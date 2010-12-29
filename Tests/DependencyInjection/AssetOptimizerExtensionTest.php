<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\Tests\DependencyInjection;

use Bundle\Adenclassifieds\AssetOptimizerBundle\DependencyInjection;

/**
 * Exposer for the get file name method
 *
 * @author dstendardi
 */
class AssetOptimizerExtensionTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @test
   * @cover AssetOptimizerExtension::configLoad
   */
  public function testConfigLoad()
  {
      $extension = $this->getMockBuilder('Bundle\Adenclassifieds\AssetOptimizerBundle\DependencyInjection\AssetOptimizerExtension')
      ->disableOriginalConstructor()
      ->disableOriginalClone()
      ->setMethods(array('loadDefaults'))
      ->getMock();

      $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
      $container
      ->expects($this->once())
      ->method('hasDefinition')
      ->with('assetoptimizer')
      ->will($this->returnValue(false));

      $extension
      ->expects($this->once())
      ->method('loadDefaults')
      ->with($container);

      $extension->configLoad(array(), $container);
  }
}