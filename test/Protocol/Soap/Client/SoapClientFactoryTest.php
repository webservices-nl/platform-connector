<?php

namespace WebservicesNl\Test\Protocol\Soap\Client;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Common\Exception\Server\NoServerAvailableException;
use WebservicesNl\Platform\Webservices\PlatformConfig;
use WebservicesNl\Protocol\Soap\Client\SoapFactory;
use WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter;
use Psr\Log\LoggerInterface;
use WebservicesNl\Protocol\Soap\Client\SoapClient;

/**
 * Class SoapClientFactoryTest.
 */
class SoapClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws InputException
     * @throws \InvalidArgumentException
     * @throws NoServerAvailableException
     */
    public function testInstanceWithoutMandatoryValues()
    {
        $config = new PlatformConfig();
        $client = SoapFactory::build($config)->create();

        static::assertInstanceOf(SoapClient::class, $client);
    }

    /**
     * Test instance with Monolog passed.
     *
     * @throws InputException
     * @throws \InvalidArgumentException
     */
    public function testInstanceWithoutLogger()
    {
        $config = new PlatformConfig();
        $factory = SoapFactory::build($config);

        static::assertFalse($factory->hasLogger());
    }

    /**
     * Test instance with Monolog passed.
     *
     * @throws InputException
     * @throws \InvalidArgumentException
     * @throws NoServerAvailableException
     */
    public function testInstanceWithLogger()
    {
        $handler = new TestHandler();
        $logger = new Logger('unit-test', [$handler]);

        $config = new PlatformConfig();
        $factory = SoapFactory::build($config);
        $factory->setLogger($logger);

        $soapClient = $factory->create(
            [
                'username' => 'johndoe',
                'password' => 'fakePassword',
            ]
        );

        static::assertAttributeInstanceOf(LoggerInterface::class, 'logger', $factory);
        static::assertTrue($factory->hasLogger());
        static::assertAttributeInstanceOf(LoggerInterface::class, 'logger', $soapClient);
        static::assertTrue($handler->hasInfoThatContains('Created SoapClient for Webservices'));
        static::assertTrue($handler->hasDebugThatContains('Created SoapClient'));
        static::assertInstanceOf(Converter::class, $soapClient->getConverter());
    }

    /**
     * Rest instance with custom SoapHeader.
     *
     * @throws InputException
     * @throws \InvalidArgumentException
     * @throws NoServerAvailableException
     */
    public function testInstanceWithCustomSoapHeader()
    {
        $soapHeader = new \SoapHeader('http://www.anotherdomain.nl/', 'blahblah', 'banana');
        $config = new PlatformConfig();
        $factory = SoapFactory::build($config);

        $soapClient = $factory->create(
            [
                'username' => 'johndoe',
                'password' => 'fakePassword',
                'soapHeaders' => [$soapHeader],
            ]
        );

        static::assertAttributeContains($soapHeader, '__default_headers', $soapClient);
    }

    /**
     * Rest instance with custom endpoint.
     *
     * @throws InputException
     * @throws \InvalidArgumentException
     * @throws NoServerAvailableException
     */
    public function testInstanceWithCustomEndpoint()
    {
        $customUrl = 'https://api.webservices.nl/dutchbusiness/soap?wsdl';

        $config = new PlatformConfig();
        $factory = SoapFactory::build($config);
        $soapClient = $factory->create(
            [
                'username' => 'johndoe',
                'password' => 'fakePassword',
                'url' => $customUrl,
            ]
        );

        static::assertEquals($customUrl, $soapClient->getHttpClient()->getConfig()['base_url']);
    }
}
