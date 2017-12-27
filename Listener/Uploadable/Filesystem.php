<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

class Filesystem extends \Gaufrette\Filesystem
{
    /**
     * @var string
     */
    protected $baseUrl;

    public function setBaseUrl(?string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }
}
