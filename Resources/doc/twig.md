# Twig extension documentation

## Functions

### fsi_file_path

This function generate **relative** url to ``FSi\DoctrineExtensions\Uploadable\File`` resource.
If file is saved in not local filesystem, like FTP extension will create local adapter and save file into it.
Usage:

```
<a href="{{ fsi_file_path(entity.file, 'uploaded') }}">File</a>
```

First argument must be an instance of ``FSi\DoctrineExtensions\Uploadable\File``. The second one is prefix added to 
url (its optional).
Lets assume that ``entity.file`` is an instance of ``FSi\DoctrineExtensions\Uploadable\File`` with key 
``Entity/File/1/file.jpg``. Above code will give us:

```
<a href="/uploaded/Entity/File/1/file.jpg">File</a>
```
**uploaded** is a root folder in our web directory for uploaded file. 

There is also a possibility to set global prefix that will be used always when second parameter is not passed.
This can be done in ``app/config/config.yml``

```
twig:
    # ...
    globals:
        fsi_file_prefix: uploaded
```

From now following code

```
<a href="{{ fsi_file_path(entity.file) }}">File</a>
```

will give us

```
<a href="/uploaded/Entity/File/1/file.jpg">File</a>
```

### is_fsi_file 

This function check if passed argument is an instance of ``FSi\DoctrineExtensions\Uploadable\File``

## Filters

### fsi_file_basename

Because uplodable extensions is based on Gaufrette file names (keys) could be very long, for example:
``FSiBundleCompanySiteBundleEntityArticle/thumbnailFileKey/249/image.jpg``
To get only file name ``image.jpg`` you can use ``fsi_file_basename`` filter and it can be done like in following example:

```
<a href="#">{{ entity.file|fsi_file_basename }}</a>
```

This will give us

```
<a href="#">image.jpg</a>
```
