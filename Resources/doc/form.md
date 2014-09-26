# Form types

Additional form types associated with uploadable files. All of these form types require a field that holds 
``FSi\DoctrineExtensions\Uploadable\File`` or ``null`` in their form data.

## fsi_file form type

This type can display URL to the uploaded file below file input 

Usage:
```php
$form = $formFactory->create('form', $entity);
$form->add('file', 'fsi_file');
```

## fsi_image form type

It extends image form type from symfony and displays uploaded image preview instead of its URL

Usage:
```php
$form = $formFactory->create('form', $entity);
$form->add('file', 'fsi_image');
```

## fsi_removable_file form type

This form type is a compound type that contains two children: first of type ``fsi_file`` and second of
type ``checkbox``. The first one behaves normally and when the second checkbox is checked then previously
uploaded file is removed from form data.

Usage:
```php
$form = $formFactory->create('form', $entity);
$form->add('file', 'fsi_removable_file', array(
    'file_type' => 'fsi_image',
    'remove_options' => array(
        'label' => 'check this if you want to remove the file'
    )
));
```

Options:
* ``remove_name``, type: ``string``, default: ``remove`` - name of the second form that removes uploaded file
* ``remove_type``, string: ``string``, default: ``checkbox`` - type of the form that removes uploaded file
* ``remove_options``, type: ``array``, default: ``array('label' => 'fsi_removable_file.remove', 'mapped' => false,
  'translation_domain' => 'FSiDoctrineExtensionsBundle')`` - additional options that will be passed to the second form
* ``file_type``, type: ``string``, default: ``fsi_file`` - type of the file form
* ``file_options``, type: ``array``, default: ``array('label' => false)`` - additional options that will be passed to
  the file form
