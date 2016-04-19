<?php

namespace WebservicesNl\Test\Soap\Client;

use League\FactoryMuffin\Facade as FactoryMuffin;
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
     *
     */
    public static function setupBeforeClass()
    {
        FactoryMuffin::setCustomSaver(function () {
            return true;
        });

        FactoryMuffin::setCustomSetter(function ($object, $name, $value) {
            $name = 'set' . ucfirst(strtolower($name));
            if (method_exists($object, $name)) {
                $object->{$name}($value);
            }
        });
        FactoryMuffin::loadFactories(dirname(dirname(__DIR__)) . '/Factories');
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithoutMandatoryValues()
    {
        $config = new Config();
        $client = SoapFactory::build($config)->create([]);

        self::assertInstanceOf('WebservicesNl\Soap\Client\SoapClient', $client);
    }


    /**
     * test instance with Monolog passed
     *
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \PHPUnit_Framework_AssertionFailedError
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
                '',
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
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithCustomSoapHeader()
    {
        $soapHeader = new \SoapHeader('http://www.anotherdomain.nl/', 'blahblah', 'topsecret');
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
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Client\Input\InvalidException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceWithCustomEndpoint()
    {
        $config = new Config();
        $factory = SoapFactory::build($config);

        $soapClient = $factory->create(
            [
                'username'    => 'johndoe',
                'password'    => 'fakePassword',
                'endPoints' => ['http://www.webservicex.com/globalweather.asmx?WSDL'],
            ]
        );

        self::assertEquals(
            'http://www.webservicex.com/globalweather.asmx?WSDL',
            $soapClient->getHttpClient()->getConfig()['base_url']
        );
    }
}
