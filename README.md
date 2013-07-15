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

# Documentation

## Twig Extensions
* [fsi_file_asset](Resources/doc/twig.md)
## Symfony Form
* [fsi_file](Resources/doc/form.md)