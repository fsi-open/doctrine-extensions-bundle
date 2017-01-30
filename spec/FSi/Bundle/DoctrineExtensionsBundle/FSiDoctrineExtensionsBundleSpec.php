<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle;

use PhpSpec\ObjectBehavior;

class FSiDoctrineExtensionsBundleSpec extends ObjectBehavior
{
    function it_should_be_instance_of_bundle()
    {
        $this->shouldImplement('Symfony\Component\HttpKernel\Bundle\BundleInterface');
    }
}
