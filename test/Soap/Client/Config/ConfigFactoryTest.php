<?php

namespace Webservicesnl\Test\Soap\Client\Config;

use Webservicesnl\Soap\Client\Config\ConfigFactory;

/**
 * Class ConfigFactoryTest.
 */
class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Webservicesnl\Exception\Client\Input\InvalidException
     * @expectedExceptionMessage That just won't jive
     * @throws \Webservicesnl\Exception\Client\Input\InvalidException
     */
    public function testInstanceWithoutArguments()
    {
        ConfigFactory::config(null, null);
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     * @throws \Webservicesnl\Exception\Client\InputException
     */
    public function testInstanceWithArguments()
    {
        ConfigFactory::config('Webservices', []);
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\InputException
     * @expectedExceptionMessage Could not find a config for 'FakePlatform'
     * @throws \Webservicesnl\Exception\Client\InputException
     */
    public function testInstanceWithBadPlatform()
    {
        ConfigFactory::config('FakePlatform', []);
    }
}
