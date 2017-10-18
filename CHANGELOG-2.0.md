# Changelog for version 2.0

This is a list of changes done in version 2.0.

## Dropped support for PHP below 7.1

To be able to fully utilize new functionality introduced in 7.1, we have decided
to only support PHP versions equal or higher to it.

## Deleted filePath() method from FSiFilePathResolver

Since it required a temporary file in order to create a path, it was replaced
with `fileUrl()` method. Below is a list of classes and methods removed alongside
this change:

<table>
    <thead>
        <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Replaced with</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>parameter</td>
            <td>%fsi_doctrine_extensions.default.filesystem.adapter.prefix%</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Twig function</td>
            <td>fsi_file_path()</td>
            <td>fsi_file_url()</td>
        </tr>
        <tr>
            <td>Twig function</td>
            <td>fileAsset()</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Compiler pass</td>
            <td>FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\TwigGlobalsPass</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Twig extension</td>
            <td>FSi\Bundle\DoctrineExtensionsBundle\Twig\FSi\File</td>
            <td>-</td>
        </tr>
    </tbody>
</table>

## Moved is_fsi_file to Assets Twig Extension

Since it was the only function it provided, the `FSi\Bundle\DoctrineExtensionsBundle\Twig\FSi\File`
class was removed.
