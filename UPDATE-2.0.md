# This is a guide on how to update from version 1.x to 2.0

## Upgrade to PHP 7.1 or higher

In order to use this bundle, you will need PHP 7.1 or higher.

## Do not use deleted methods and classes

Refer to to the [CHANGELOG](CHANGELOG-2.0.md) on information on what has been
removed.

## Make use of `'file_url'` form variable in file form's view

Instead of calling `fsi_file_url(form.vars.data)` you can use the prepared
`form.vars.file_url` variable. This change is not necessary, however it is
required if you want to use the new `'file_url'` file form's option.

## FileType has the new constructor with two arguments

You should probably update any class that inherits from the original
`FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType`.
