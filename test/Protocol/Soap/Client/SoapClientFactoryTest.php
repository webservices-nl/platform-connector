<?php

namespace WebservicesNl\Test\Protocol\Soap\Client;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use WebservicesNl\Platform\Webservices\PlatformConfig;
use WebservicesNl\Protocol\Soap\Client\SoapFactory;

/**
 * Class SoapClientFactoryTest.
 */
class SoapClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithoutMandatoryValues()
    {
        $config = new PlatformConfig();
        $client = SoapFactory::build($config)->create();

        static::assertInstanceOf('WebservicesNl\Protocol\Soap\Client\SoapClient', $client);
    }

    /**
     * Test instance with Monolog passed.
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
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
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
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

        static::assertAttributeInstanceOf('\Psr\Log\LoggerInterface', 'logger', $factory);
        static::assertTrue($factory->hasLogger());
        static::assertAttributeInstanceOf('\Psr\Log\LoggerInterface', 'logger', $soapClient);
        static::assertTrue($handler->hasInfoThatContains('Created SoapClient for Webservices'));
        static::assertTrue($handler->hasDebugThatContains('Created SoapClient'));
        static::assertInstanceOf(
            'WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter',
            $soapClient->getConverter()
        );
    }

    /**
     * Rest instance with custom SoapHeader.
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
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
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithCustomEndpoint()
    {
        $customUrl = 'http://www.thomas-bayer.com/axis2/services/BLZService?wsdl';

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
