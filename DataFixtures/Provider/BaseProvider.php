<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DataFixtures\Provider;

use Faker\Generator;
use Faker\Provider\Base;

abstract class BaseProvider extends Base
{
    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $generator->addProvider($this);
        parent::__construct($generator);
    }
}
