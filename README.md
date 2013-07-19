#FSi Doctrine extensions bundle

This bundle should be used to simplify fsi doctrine extensions registration.

##Installation

modify composer.json file

```
{
    "repositories": [{ "type": "composer", "url": "http://git.fsi.pl"}],
    "require": {
        "fsi/doctrine-extensions-bundle": "1.0.x-dev",
    },

}
```
Execute:

```
php composer.phar update
```

Modify Files:

```php
    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new FSi\Bundle\DoctrineExtensionsBundle\FSiDoctrineExtensionsBundle(),
        );
    }
```

Configure listeners

**Listeners are not registered by default and you need to configure them in ``app/config/config.yml`` file before using.**

```
# app/config/config.yml

fsi_doctrine_extensions:
    orm:
        default:
            uploadable: true
    default_key_maker_service: fsi_doctrine_extensions.default.key_maker # optional id of default key maker service
    default_filesystem_path: "%kernel.root_dir%/../web/uploaded" # optional root path for default filesystem
    default_filesystem_service: fsi_doctrine_extensions.default.filesystem # optional default filesystem
```

Sometimes you also may need to configure filesystem or keymaker for entity not in metadata configuration.
For example if you create a bundle that provide entity that use uploadable extension you can't force bundle users to
use filesystem defined in bundle. Filesystem is something that should depend from application, not bundle.
For such cases you can use ``uploadable_configuration`` option in application config.

```
knp_gaufrette:
    adapters:
        local_adapter:
            local:
                directory: "%kernel.root_dir%/../uploaded"
    filesystems:
        article_content_image:
            adapter: local_adapter

fsi_doctrine_extensions:
    uploadable_configuration:
        FSi\Bundle\CompanySiteBundle\Entity\ArticleContent: # class that use uploadable extension
            configuration:
                imageFileKey: # property that is file key
                    filesystem: article_content_image # gaufrette filesystem
```
From now ``FSi\Bundle\CompanySiteBundle\Entity\ArticleContent::imageFileKey`` will use ``article_content_image`` filesystem.
Even if there is other filesystem defined in field metadata. 

# Documentation

## Twig Extensions
* [fsi_file_asset](Resources/doc/twig.md)
## Symfony Form
* [fsi_file](Resources/doc/form.md)