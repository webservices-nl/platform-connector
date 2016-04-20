<?php

namespace WebservicesNl\Test\Soap\Client\Config;

use WebservicesNl\Soap\Config\ConfigFactory;

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
        $config = \Mockery::mock('WebservicesNl\Connector\Platform\PlatformConfigInterface');
        $config->shouldReceive('getPlatformName')->andReturn('Fake');

        /** @var \WebservicesNl\Connector\Platform\PlatformConfigInterface $config */
        ConfigFactory::config($config);
    }
}
