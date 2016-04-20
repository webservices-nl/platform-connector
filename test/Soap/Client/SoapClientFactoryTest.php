<?php

namespace WebservicesNl\Test\Soap\Client;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use WebservicesNl\Connector\Platform\Webservices\Config;
use WebservicesNl\Soap\Client\SoapFactory;

/**
 * Class SoapClientFactoryTest.
 */
class SoapClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithoutMandatoryValues()
    {
        $config = new Config();
        $client = SoapFactory::build($config)->create();

        self::assertInstanceOf('WebservicesNl\Soap\Client\SoapClient', $client);
    }

    /**
     * Test instance with Monolog passed
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithLogger()
    {
        $handler = new TestHandler();
        $logger = new Logger('unit-test', [$handler]);

        $soapHeader = new \SoapHeader('http://www.somedomain.nl/', 'lala', 'hihi');

        $config = new Config();
        $factory = SoapFactory::build($config);
        $factory->setLogger($logger);

        $soapClient = $factory->create(
            [
                'username'    => 'johndoe',
                'password'    => 'fakePassword',
                'soapHeaders' => [$soapHeader],
            ]
        );

        static::assertAttributeInstanceOf('\Psr\Log\LoggerInterface', 'logger', $factory);
        static::assertAttributeInstanceOf('\Psr\Log\LoggerInterface', 'logger', $soapClient);
        static::assertTrue($handler->hasInfoThatContains('Creating a SoapClient to connect to platform Webservices'));
        static::assertTrue($handler->hasDebugThatContains('Created SoapClient'));
    }

    /**
     * Rest instance with custom SoapHeader
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithCustomSoapHeader()
    {
        $soapHeader = new \SoapHeader('http://www.anotherdomain.nl/', 'blahblah', 'banana');
        $config = new Config();
        $factory = SoapFactory::build($config);

        $soapClient = $factory->create(
            [
                'username'    => 'johndoe',
                'password'    => 'fakePassword',
                'soapHeaders' => [$soapHeader],
            ]
        );

        static::assertAttributeContains($soapHeader, '__default_headers', $soapClient);
    }

    /**
     * Rest instance with custom endpoint
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceWithCustomEndpoint()
    {
        $customUrl = 'http://www.webservicex.com/globalweather.asmx?WSDL';

        $config = new Config();
        $factory = SoapFactory::build($config);
        $soapClient = $factory->create(
            [
                'username' => 'johndoe',
                'password' => 'fakePassword',
                'url'      => $customUrl,
            ]
        );

        self::assertEquals($customUrl, $soapClient->getHttpClient()->getConfig()['base_url']);
    }
}
