# Form types

Expanded file types that generate url to resource.

## fsi_file form type

Usage:
```php
$form = $formFactory->create('form', $entity);
$form->add('file', 'fsi_file');
```

## fsi_image form type

Usage:
```php
$form = $formFactory->create('form', $entity);
$form->add('file', 'fsi_image');
```

In both cases there must be a field ``file`` in ``$entity`` that returns ``FSi\DoctrineExtensions\Uploadable\File`` or ``null``.
