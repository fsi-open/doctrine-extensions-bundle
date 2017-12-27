<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\DependencyInjection;

use FSi\Bundle\DemoBundle\Article;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{

    public function testDefaultOptions()
    {
        $config = $this->getProcessor()->processConfiguration(new Configuration(), []);

        $this->assertSame(
            $config,
            self::getBundleDefaultOptions()
        );
    }

    public function testUploadableEnabledOption()
    {
        $config = $this->getProcessor()->processConfiguration(
            new Configuration(),
            [0 => ['orm' => ['default' => ['uploadable' => true]]]]
        );

        $this->assertSame(
            $config,
            array_merge(
                $this->getBundleDefaultOptions(),
                ['orm' => ['default' => ['uploadable' => true, 'translatable' => false]]]
            )
        );
    }

    public function testUploadableConfigurationOptions()
    {
        $input = [[
            'orm' => ['default' => ['uploadable' => true]],
            'uploadable_configuration' => [
                [
                    'class' => 'FSi\Bundle\DemoBundle\Article',
                    'configuration' => [
                        [
                            'property' => 'file',
                            'filesystem' => 'default_filesystem',
                            'keymaker' => 'default_keymaker',
                        ],
                        [
                            'property' => 'image_file',
                            'filesystem' => 'default_filesystem',
                            'keymaker' => 'default_keymaker',
                        ]
                    ]
                ],
            ]
        ]];
        $config = $this->getProcessor()->processConfiguration(new Configuration(), $input);

        $this->assertSame(
            $config,
            array_merge(
                [
                    'orm' => ['default' => ['uploadable' => true, 'translatable' => false]],
                    'uploadable_configuration' => [
                        Article::class => [
                            'configuration' => [
                                'file' => [
                                    'filesystem' => 'default_filesystem',
                                    'keymaker' => 'default_keymaker',
                                ],
                                'image_file' => [
                                    'filesystem' => 'default_filesystem',
                                    'keymaker' => 'default_keymaker',
                                ]
                            ]
                        ]
                    ],
                    'uploadable_filesystems' => [],
                    'default_locale' => '%locale%',
                    'default_key_maker_service' =>  'fsi_doctrine_extensions.default.key_maker',
                    'default_filesystem_prefix' => 'uploaded',
                    'default_filesystem_base_url' => '/uploaded',
                    'default_filesystem_path' => '%kernel.root_dir%/../web/uploaded',
                    'default_filesystem_service' => 'fsi_doctrine_extensions.default.filesystem',
                ]
            )
        );
    }

    public function testTranslatableEnabledOption()
    {
        $input = [['orm' => ['default' => ['translatable' => true]]]];
        $config = $this->getProcessor()->processConfiguration(new Configuration(), $input);

        $this->assertSame(
            $config,
            array_merge(
                $this->getBundleDefaultOptions(),
                ['orm' => ['default' => ['translatable' => true, 'uploadable' => false]]]
            )
        );
    }

    public function testUploadableFilesystemsOptions()
    {
        $filestystemsConfig = [
            'uploadable_filesystems' => [
                'default' => ['base_url' => '/'],
                'remote_filesystem' => ['base_url' => 'http://domain.com/basepath/']
            ]
        ];
        $config = $this->getProcessor()->processConfiguration(new Configuration(), [$filestystemsConfig]);

        $this->assertSame(
            $config['uploadable_filesystems'],
            $filestystemsConfig['uploadable_filesystems']
        );
    }

    public static function getBundleDefaultOptions()
    {
        return [
            'orm' => [],
            'uploadable_filesystems' => [],
            'default_locale' => '%locale%',
            'default_key_maker_service' =>  'fsi_doctrine_extensions.default.key_maker',
            'default_filesystem_prefix' => 'uploaded',
            'default_filesystem_base_url' => '/uploaded',
            'default_filesystem_path' => '%kernel.root_dir%/../web/uploaded',
            'default_filesystem_service' => 'fsi_doctrine_extensions.default.filesystem',
            'uploadable_configuration' => []
        ];
    }

    protected function getProcessor(): Processor
    {
        return new Processor();
    }
}
