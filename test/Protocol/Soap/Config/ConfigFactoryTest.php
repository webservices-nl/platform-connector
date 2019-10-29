<?php

namespace WebservicesNl\Test\Protocol\Soap\Client\Config;

use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Platform\PlatformConfigInterface;
use WebservicesNl\Protocol\Soap\Config\ConfigFactory;

/**
 * Class ConfigFactoryTest.
 */
class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws InputException
     */
    public function testInstanceWithBadPlatform()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Could not find a platform config for \'Fake\'');
        $config = \Mockery::mock(PlatformConfigInterface::class);
        $config->shouldReceive('getPlatformName')->andReturn('Fake');

        /* @var PlatformConfigInterface $config */
        ConfigFactory::config($config);
    }
}
