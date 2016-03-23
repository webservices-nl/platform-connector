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
     * @expectedExceptionMessage Could not find a platform config for ''
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithoutArguments()
    {
        ConfigFactory::config(null, null);
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithArguments()
    {
        ConfigFactory::config('Webservices', []);
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not find a platform config for 'FakePlatform'
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithBadPlatform()
    {
        ConfigFactory::config('FakePlatform', []);
    }
}
