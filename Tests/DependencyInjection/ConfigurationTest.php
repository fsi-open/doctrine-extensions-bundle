<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\DependencyInjection;

use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOptions()
    {
        $config = $this->getProcessor()->processConfiguration(new Configuration(), array());

        $this->assertSame(
            $config,
            self::getBundleDefaultOptions()
        );
    }

    public function testUploadableDefaultOptions()
    {
        $input = array(
            0 => array(
                'orm' => array(
                    'default' => array(
                        'uploadable' => true
                    )
                )
            )
        );
        $config = $this->getProcessor()->processConfiguration(new Configuration(), $input);

        $this->assertSame(
            $config,
            array_merge(
                $this->getBundleDefaultOptions(),
                array(
                    'orm' => array(
                        'default' => array(
                            'uploadable' => true
                        )
                    )
                )
            )
        );
    }

    public static function getBundleDefaultOptions()
    {
        return array(
            'orm' => array(),
            'default_key_maker_service' =>  'fsi_doctrine_extensions.default.key_maker',
            'default_filesystem_path' => '%kernel.root_dir%/../web/uploaded',
            'default_filesystem_service' => 'fsi_doctrine_extensions.default.filesystem'
        );
    }

    /**
     * @return Processor
     */
    protected function getProcessor()
    {
        return new Processor();
    }
}