Provides "on the fly" assets compression.

## Installation

### Add AssetOptimizerBundle to your src/Bundle dir

    git submodule add git://github.com/Adenclassifieds/AssetOptimizerBundle.git src/Bundle/Adenclassifieds/AssetOptimizerBundle
    
### Add AssetOptimizerBundle to your application Kernel


    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            //..
            new Bundle\Adenclassifieds\AssetOptimizerBundle\AssetOptimizerBundle(),
            //..
        );    
    }
    
### Setup file compression in your config.yml

    assetoptimizer.config:
        javascripts: ~
        stylesheets: ~
        # optionals (with default values)
        # assets_path: %kernel.root_dir%/../web
        # cache_path: %kernel.root_dir%/../web/cache

### Add a cache directory inside the assets folder

This can be done in two ways.

1. Manually create the cache directory (do not forget to give write permissions).

       mkdir -p path/to/cache && chmod 775 path/to/cache

2. Let the bundled clear-cache command do the work for you. When the cache is cleared it will remove the cache folder and recreate it.

       php app/console assets:optimizer:clear-cache

**If this is not done a error with `cannot write to /compressed-823782837283723823gdasdhjad.css` which is because realpath is trying
to resolve a non existing path and therefor resolves it to root**

### There is no changes inside templates

the following code

    <?php $view['stylesheets']->add('foo/bar.css'); ?>
    <?php $view['stylesheets']->add('bar/foo.css'); ?>
    <?php $view['stylesheets']->add('http://foo.com/bar.css'); ?>
    <?php echo $view['stylesheets'] ?>
    
should generate a file and produces (note that externals assets are ignored):

    <link href="http://assets.mysite.com/cache/compressed-f71dbe52628a3f83a77ab494817525c6.css" rel="stylesheet" type="text/css" />
    <link href="http://foo.com/bar.css" rel="stylesheet" type="text/css" />

#### Standalone assets

As a feature that only is supported when using this bundle is the term of "standalone" assets. Which means a asset marked as "standalone"
will not be combing and/or minified.

    {% javascript 'path/to/file.js' with { 'standalone' : true } %}

    <?php $view['javascripts']->add('path/to/file.js', array('standalone' => true)); ?>

and the same for stylesheets

    {% stylesheet 'path/to/file.css' with { 'standalone' : true } %}

    <?php $view['stylesheets']->add('path/to/file.css', array('standalone' => true)); ?>

## Command lines


### Clear the generated cache files

    console assets:optimizer:clear-cache


## How to extend the asset optimizer

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

## Change optimizer classes

You can use your own javascript and stylesheet optimizer by changing the class parameters:

    assetoptimizer.config:
        javascripts: 
            class: My\Javascript\Optimizer
        stylesheets:
            class: My\Stylesheet\Optimizer

## Vendor

In order to ease the setup, this bundle contains two vendor libraries : [Minify](http://code.google.com/p/minify/wiki/ComponentClasses) & [JavascriptPacker](http://joliclic.free.fr/php/javascript-packer/en/)

It clearly goes against one of the [best practices](http://docs.symfony-reloaded.org/guides/bundles/best_practices.html), but there is no easy way to setup required dependency yet.
