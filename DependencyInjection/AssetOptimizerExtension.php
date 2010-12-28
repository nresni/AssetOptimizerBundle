<?php
namespace Bundle\Adenclassifieds\AssetOptimizerBundle\DependencyInjection;

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

        if (isset($config['assets_path'])) {
            $container->setParameter('assetoptimizer.assets_path', $config['assets_path']);
        }

        if (isset($config['cache_path'])) {
            $container->setParameter('assetoptimizer.cache_path', $config['cache_path']);
        }

        foreach(array('javascript', 'stylesheet') as $type) {

            $plural = $type.'s';

            if (array_key_exists($plural, $config)) {
                $container->getDefinition('templating.helper.'.$type)->addMethodCall('setOptimizer', array(new Reference('asset.optimizer.'.$type)));
            }
            if(isset($config[$plural]['class'])) {
                $container->setParameter(sprintf('asset.optimizer.%s.class', $type), $config[$plural]['class']);
            }
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    /**
     *
     * Enter description here ...
     */
    public function getNamespace()
    {
        return null;
    }

    /**
     *
     * Enter description here ...
     */
    public function getAlias()
    {
        return 'assetoptimizer';
    }
}
