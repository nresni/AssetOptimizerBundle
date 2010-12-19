Provides "on the fly" assets compression.

## Installation

### Add AssetOptimizerBundle to your src/Bundle dir

    git submodule add git://github.com/dstendardi/AssetOptimizerBundle.git src/Bundle/AssetOptimizerBundle
    
### Add AssetOptimizerBundle to your application Kernel


    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            //..
            new Bundle\AssetOptimizerBundle\AssetOptimizerBundle(),
            //..
        );    
    }
    
### Setup file compression in your config.yml

    assetoptimizer.config:
        assets_path: %kernel.root_dir%/../web/assets
        javascripts: ~
        stylesheets: ~

### There is no changes inside templates

the following code

    <?php $view['stylesheets']->add('/foo/bar.css'); ?>
    <?php echo $view['stylesheets'] ?>
    
should generate a file and produces:

    <link href="http://assets.mysite.com/cache/compressed-f71dbe52628a3f83a77ab494817525c6.css" rel="stylesheet" type="text/css" />

## Extend the asset optimizer

If you wish to add some unsupported behavior to the asset optimizer, feel free to use the following events


###  assetoptimizer.filter_resources

This event is triggered just before the resources are optimized.
Here is an exemple of code that checks for attributes "sprite-suffix" and replace the resource url with the sprited css

    /**
     * @param Event
     * @param array resources
     */
    public function filterResources($event, $resources)
    {
        foreach ($resources as $url => $attributes) {
            if (isset($resource['sprite-suffix'])) {
                $spriteUrl = str_replace('.css', 'sprite.css', $url);
                unset($attributes['sprite-suffix']);
                $resources[$spriteUrl] = $attributes;
                unset($resources[$url]);
            }
        }
        return $resources;
    }

## Vendor

In order ot ease the setup, this bundle contains two vendor libraries : [Minify](http://code.google.com/p/minify/wiki/ComponentClasses) & [JavascriptPacker](http://joliclic.free.fr/php/javascript-packer/en/)

It clearly goes against one of the [best practices](http://docs.symfony-reloaded.org/guides/bundles/best_practices.html), but there is no easy way to setup required dependency yet.