# fsi_file form type

Expanded file type that generate url to resource.  
Usage:
```php
$form = $formFactory->create('form', $entity);
$form->add('file', 'fsi_file');
```
There must be a field ``file`` in ``$entity`` that returns ``FSi\DoctrineExtensions\Uploadable\File`` or ``null``.