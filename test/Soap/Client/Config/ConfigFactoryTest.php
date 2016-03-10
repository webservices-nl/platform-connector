<?php

namespace Webservicesnl\Test\Soap\Client\Config;

use Webservicesnl\Soap\Client\Config\ConfigFactory;

/**
 * Class ConfigFactoryTest.
 */
class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Webservicesnl\Common\Exception\Client\InputException
     * @expectedExceptionMessage That just won't jive
     *
     * @throws \Webservicesnl\Common\Exception\Client\InputException
     */
    public function testInstanceWithoutArguments()
    {
        ConfigFactory::config(null, null);
    }

    /**
     * @expectedException \Webservicesnl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \Webservicesnl\Common\Exception\Client\InputException
     */
    public function testInstanceWithArguments()
    {
        ConfigFactory::config('Webservices', []);
    }

    /**
     * @expectedException \Webservicesnl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not find a config for 'FakePlatform'
     *
     * @throws \Webservicesnl\Common\Exception\Client\InputException
     */
    public function testInstanceWithBadPlatform()
    {
        ConfigFactory::config('FakePlatform', []);
    }
}
