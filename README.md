# FSi DoctrineExtensionsBundle

This bundle provides integration with the [FSi DoctrineExtensions library.](https://github.com/fsi-open/doctrine-extensions)

## Installation

This is the master branch, which is under development. For a stable release, please
use version `1.1`.

Add the bundle to `composer.json` and run `composer.phar update`.

```json
{
    "require": {
        "fsi/doctrine-extensions-bundle": "2.0@dev",
    }
}
```

### Register bundles in AppKernel.php

```php
    // app/AppKernel.php

    public function registerBundles()
    {
        return [
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new FSi\Bundle\DoctrineExtensionsBundle\FSiDoctrineExtensionsBundle(),
        ];
    }
```

### Configure listeners

Listeners are not registered by default and you need to enable them in the
``app/config/config.yml`` file before using.

```yaml
# app/config/config.yml

fsi_doctrine_extensions:
    orm:
        default:
            translatable: true
            uploadable: true
```

Be sure to enable the translations in `app/config/config.yml`, even if you do not
wish to use the translatable component of the bundle.

```yaml
framework:
    translator:      { fallback: %locale% }
```

## Example of an entity with an uploadable field

```php
<?php

namespace FSi\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints as FSiAssert;
use FSi\DoctrineExtensions\Uploadable\Mapping\Annotation as FSi;
use Symfony\Component\Validator\Constraints as Assert;

/**
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
    private $id;

    /**
     * It is important that this column is nullable, because the value is set
     * after the entity is persisted.
     *
     * @ORM\Column(nullable=true)
     * @FSi\Uploadable(targetField="photo")
     */
    private $photoKey;

    /**
     * Currently there is no common interface for uploaded files.
     *
     * @var mixed
     *
     * @FSiAssert\Image(
     *     maxWidth = 1000,
     *     maxHeight = 460
     * )
     */
    private $photo;

    public function getId(): int
    {
        return $this->id;
    }

    public function setPhoto($photo): void
    {
        $this->photo = $photo;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhotoKey(?string $photoKey): void
    {
        $this->photoKey = $photoKey;
    }

    public function getPhotoKey(): ?string
    {
        return $this->photoKey;
    }
}

```

## Example of an entity with translatable fields and a translation entity

```php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FSi\DoctrineExtensions\Translatable\Mapping\Annotation as Translatable;

/**
 * The entity's repository needs to implement the 
 * \FSi\DoctrineExtensions\Translatable\Model\TranslatableRepositoryInterface
 *
 * @ORM\Entity(repositoryClass="FSi\DoctrineExtensions\Translatable\Entity\Repository\TranslatableRepository")
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @Translatable\Locale
     */
    private $locale;

    /**
     * @var string
     *
     * @Translatable\Translatable(mappedBy="translations")
     */
    private $title;

    /**
     * @var string
     *
     * @Translatable\Translatable(mappedBy="translations")
     */
    private $contents;

    /**
     * @var Collection|ArticleTranslation[]
     *
     * @ORM\OneToMany(targetEntity="ArticleTranslation", mappedBy="article", indexBy="locale")
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setContents(?string $contents): void
    {
        $this->contents = $contents;
    }

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }
}
```

```php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use FSi\DoctrineExtensions\Translatable\Mapping\Annotation as Translatable;

/**
 * @ORM\Entity
 */
class ArticleTranslation
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @Translatable\Locale
     *
     * @ORM\Column(length=2)
     */
    private $locale;

    /**
     * @ORM\Column
     * @var string
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $contents;

    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="translations")
     */
    private $article;

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setContents(?string $contents): void
    {
        $this->contents = $contents;
    }

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setArticle(?Article $article): void
    {
        $this->article = $article;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }
}
```

## Additional documentation

* [Twig Extensions](Resources/doc/twig.md)
* [Symfony Form](Resources/doc/form.md)
* [Request TranslatableParamConverter](Resources/doc/param_converter.md)

## Extended validators

There are two validators that can be used with FSi uploadable file. 

``@FSiAssert\Image`` - extends Symfony's Image validator  
``@FSiAssert\File`` - extends Symfony's File validator  

Both of these have exactly the same options as their parent classes.

## Detailed documentation of FSi doctrine extensions

* [Translatable](https://github.com/fsi-open/doctrine-extensions/blob/master/doc/translatable.md)
* [Uploadable](https://github.com/fsi-open/doctrine-extensions/blob/master/doc/uploadable.md)
