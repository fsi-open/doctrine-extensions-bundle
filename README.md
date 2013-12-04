#FSi Doctrine extensions bundle

This bundle simplify fsi doctrine extensions configuration.

##Installation

Modify composer.json file

```
{
    "require": {
        "fsi/doctrine-extensions-bundle": "1.0.*",
    }
}
```
Execute:

```
php composer.phar update
```
Register bundles in AppKernel.php

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
            translatable: true
            uploadable: true
```

Enable translations in app/config/config.yml

```
framework:
    translator:      { fallback: %locale% }
```

Create entity

```php
<?php

namespace FSi\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FSi\DoctrineExtensions\Uploadable\Mapping\Annotation as FSi;
use Symfony\Component\Validator\Constraints as Assert;
use FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints as FSiAssert;

/**
 * @ORM\Table(name="article")
 * @ORM\Entity()
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    protected $id = 1;

    /**
     * @ORM\Column(length=255, nullable=true, name="photo_key")
     * @FSi\Uploadable(targetField="photo")
     */
    protected $photoKey;

    /**
     * @FSiAssert\Image(
     *     maxWidth = 1000,
     *     maxHeight = 460
     * )
     */
    protected $photo;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $photo
     * @return Article
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photoKey
     * @return Article
     */
    public function setPhotoKey($photoKey)
    {
        $this->photoKey = $photoKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhotoKey()
    {
        return $this->photoKey;
    }
}

```

# Documentation

* [Twig Extensions](Resources/doc/twig.md)
* [Symfony Form](Resources/doc/form.md)

# Validation

There are two validators that can be used with FSi uploadable file. 

``@FSiAssert\Image`` - extends symfony2 Image validator  
``@FSiAssert\File`` - extends symfony2 File validator  

Both of them have exactly same options as parents. 
