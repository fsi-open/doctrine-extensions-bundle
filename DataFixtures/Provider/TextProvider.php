<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DataFixtures\Provider;

class TextProvider extends BaseProvider
{
    public function htmlSentences($count)
    {
        return sprintf('<p>%s</p>', $this->joinedSentences($count));
    }

    public function joinedSentences($count)
    {
        return implode(' ', $this->generator->sentences($count));
    }
}
