<?php

namespace WebservicesNl\Test\Soap\Client;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use WebservicesNl\Soap\Client\SoapFactory;
use WebservicesNl\Soap\Config\Webservices\Config as WebservicesConfig;

/**
 * Class SoapClientFactoryTest.
 */
class SoapClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithoutArguments()
    {
        $bla = new SoapFactory('Webservices');
        static::assertNull($bla->getLogger());
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithoutMandatoryValues()
    {
        SoapFactory::build('Webservices')->create([]);
    }

    /**
     *
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testInstanceWithLoggerAndGuzzle()
    {
        $handler = new TestHandler();
        $logger = new Logger('unit-test', [$handler]);
        $platform = WebservicesConfig::PLATFORM_NAME;

        $soapHeader = new \SoapHeader('http://www.somedomain.nl/', 'lala', 'hihi');
        $soapClient = SoapFactory::build($platform, $logger)->create(
            [
                'username'    => 'johndoe',
                'password'    => 'fakePassword',
                'soapHeaders' => [$soapHeader],
                '',
            ]
        );

        static::assertTrue($soapClient->hasClient());
        static::assertAttributeInstanceOf('\Monolog\Logger', 'logger', $soapClient);
        static::assertAttributeContains($soapHeader, '__default_headers', $soapClient);

        static::assertTrue($handler->hasInfoThatContains('Creating a SoapClient for platform Webservices'));
        static::assertTrue($handler->hasDebugThatContains('Created EndpointManager'));
        static::assertTrue($handler->hasDebugThatContains('Created SoapClient'));
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithoutLogger()
    {
        $soapClient = SoapFactory::build('Webservices')->create(['username' => 'john', 'password' => 'lalala']);
        static::assertAttributeNotInstanceOf('\Monolog\Logger', 'logger', $soapClient);
    }

    /**
     *
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testInstanceWithoutGuzzle()
    {
        $soapClient = SoapFactory::build('Webservices')->create(
            [
                'username'      => 'johndoe',
                'password'      => 'fake',
                'useHttpClient' => false,
            ]
        );

        static::assertFalse($soapClient->hasClient());
    }
    
    /**
     *
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage fakePlatform is not a valid platform
     *
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testInstanceWithBadPlatform()
    {
        $soapClient = SoapFactory::build('fakePlatform')->create(
            [
                'username'      => 'johndoe',
                'password'      => 'fake',
                'useHttpClient' => false,
            ]
        );

        static::assertFalse($soapClient->hasClient());
    }
}
