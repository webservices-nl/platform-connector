<?php

namespace WebservicesNl\Test\Protocol\Soap\Client\Config;

use WebservicesNl\Protocol\Soap\Config\ConfigFactory;

/**
 * Class ConfigFactoryTest.
 */
class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not find a platform config for 'Fake'
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithBadPlatform()
    {
        $config = \Mockery::mock('WebservicesNl\Platform\PlatformConfigInterface');
        $config->shouldReceive('getPlatformName')->andReturn('Fake');

        /** @var \WebservicesNl\Platform\PlatformConfigInterface $config */
        ConfigFactory::config($config);
    }
}
