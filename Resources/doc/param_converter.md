# TranslatableParamConverter

This bundle registers its own TranslatableParamConverter which is executed before DoctrineParamConverter and
supports all entities which have translatable properties. Take a look at the example below which show how to
automagically convert request parameters into translatable entities.

```php
# Acme/NewsBundle/Entity/News.php

namespace Acme/NewsBundle/Entity;

use Doctrine\ORM\Mapping as ORM;
use FSi\DoctrineExtensions\Translatable\Mapping\Annotation as Translatable;

/**
 * @ORM\Entity()
 */
class News
{

# ...

    /**
     * @Translatable\Translatable(mappedBy="translations")
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="NewsTranslation", mappedBy="news", indexBy="locale")
     */
    private $translations;

# ...

}
```

```yml
# Acme/NewsBundle/Resources/config/routing.yml

acme_news_details:
    pattern:  /news/{slug}
    defaults: { _controller: acme.controller.news:detailsAction }

```

```php
# Acme/NewsBundle/Controller/NewsController.php

namespace Acme/NewsBundle/Controller;

use Acme/NewsBundle/News;

class NewsController
{
# ...
    public function detailsAction(News $news)
    {

    }
# ...
}
```

``TranslatableParamConverter`` works similarly to ``DoctrineParamConverter`` with the difference it finds entities
using their regular ORM-mapped fields as well as translatable properties which are in fact stored in associated
translation entity.
