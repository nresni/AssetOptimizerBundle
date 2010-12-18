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