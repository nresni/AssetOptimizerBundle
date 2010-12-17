<?php
namespace Bundle\AssetOptimizerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * AssetOptimizerExtension.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class AssetOptimizerExtension extends Extension
{
    /**
     * Loads the AssetOptimizer configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('assetoptimizer')) {
            $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
            $loader->load('assetoptimizer.xml');
        }
        
        if (isset($config['assets.path'])) 
        {
            $container->getDefinition('asset.optimizer.stylesheet')->addMethodCall('setAssetPath', array($config['assets.path']));
        }
        
        if (array_key_exists('javascripts', $config))
        {
            $container->getDefinition('templating.helper.javascripts')->addMethodCall('setOptimizer', array(new Reference('asset.optimizer.javascript')));
        }
        
        if (array_key_exists('stylesheets', $config))
        {
            $container->getDefinition('templating.helper.stylesheets')->addMethodCall('setOptimizer', array(new Reference('asset.optimizer.stylesheet')));
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/twig';
    }

    public function getAlias()
    {
        return 'assetoptimizer';
    }
}