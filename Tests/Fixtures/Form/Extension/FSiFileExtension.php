<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension;

use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType;
use Symfony\Component\Form\AbstractExtension;

class FSiFileExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(
            new FileType()
        );
    }
}
