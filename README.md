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

## Example entity with uploadable fields

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

## Example entity with translatable fields and translation entity

```php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use FSi\DoctrineExtensions\Translatable\Mapping\Annotation as Translatable;

/**
 * @ORM\Entity(repositoryClass="\FSi\DoctrineExtensions\Translatable\Entity\Repository\TranslatableRepository")
 */
class Article
{
    /**
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer $id
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var string
     */
    private $date;

    /**
     * @Translatable\Locale
     * @var string
     */
    private $locale;

    /**
     * @Translatable\Translatable(mappedBy="translations")
     * @var string
     */
    private $title;

    /**
     * @Translatable\Translatable(mappedBy="translations")
     * @var string
     */
    private $contents;

    /**
     * @ORM\OneToMany(targetEntity="ArticleTranslation", mappedBy="article", indexBy="locale")
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setTitle($title)
    {
        $this->title = (string)$title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContents($contents)
    {
        $this->contents = (string)$contents;
        return $this;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function setLocale($locale)
    {
        $this->locale = (string)$locale;
        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function hasTranslation($locale)
    {
        return isset($this->translations[$locale]);
    }

    public function getTranslation($locale)
    {
        if ($this->hasTranslation($locale)) {
            return $this->translations[$locale];
        } else {
            return null;
        }
    }
}
```

```
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use FSi\DoctrineExtensions\Translatable\Mapping\Annotation as Translatable;

/**
 * @ORM\Entity
 */
class ArticleTranslation
{
    /**
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer $id
     */
    private $id;

    /**
     * @Translatable\Locale
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    private $locale;

    /**
     * @ORM\Column
     * @var string
     */
    private $title;

    /**
     * @ORM\Column
     * @var string
     */
    private $contents;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="translations")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $article;

    public function setTitle($title)
    {
        $this->title = (string)$title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContents($contents)
    {
        $this->contents = (string)$contents;
        return $this;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function setLocale($locale)
    {
        $this->locale = (string)$locale;
        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

}
```

## Additional documentation

* [Twig Extensions](Resources/doc/twig.md)
* [Symfony Form](Resources/doc/form.md)
* [Request TranslatableParamConverter](Resources/doc/param_converter.md)

## Extended validators

There are two validators that can be used with FSi uploadable file. 

``@FSiAssert\Image`` - extends symfony2 Image validator  
``@FSiAssert\File`` - extends symfony2 File validator  

Both of them have exactly same options as parents. 

## Detailed documentation of FSi doctrine extensions

* [Translatable](https://github.com/fsi-open/doctrine-extensions/blob/master/doc/translatable.md)
* [Uploadable](https://github.com/fsi-open/doctrine-extensions/blob/master/doc/uploadable.md)
