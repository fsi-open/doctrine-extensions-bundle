# Twig extension documentation

## Functions

### fsi_file_url

This function generates url to passed ``FSi\DoctrineExtensions\Uploadable\File`` object. This URL is generated
from base URL configured in one of two places. One for default filesystem:

```yml
fsi_doctrine_extensions:
    default_filesystem_base_url: '/uploaded'
```

And the other for any filesystem configured through KnpGaufretteBundle's configuration.

```yml
knp_gaufrette:
    adapters:
        some_adapter:
            aws_s3:
                service_id: amazon_aws_client_service_id
                bucket_name: your_bucket_name
    filesystems:
        some_filesystem:
            adapter: some_adapter

fsi_doctrine_extensions:
    uploaded_filesystems:
        some_filesystem:
            base_url: https://s3-eu-west-1.amazonaws.com/your_bucket_name
```

### is_fsi_file 

This function check if passed argument is an instance of ``FSi\DoctrineExtensions\Uploadable\File``.

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
