<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DataFixtures\Generator;

use Faker\Factory;
use Faker\Generator;

class Instantiator
{
    /**
     * @var Generator;
     */
    private $generator;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        if (!$this->generator) {
            $this->createGenerator();
        }

        return $this->generator;
    }

    /**
     * @return Generator
     */
    private function createGenerator()
    {
        $this->generator = Factory::create($this->locale);
    }
}
