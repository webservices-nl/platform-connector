<?php

namespace WebservicesNl\Test\Soap\Client;

use League\FactoryMuffin\Facade as FactoryMuffin;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use WebservicesNl\Soap\Client\Config\WebservicesConfig;
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
     *
     */
    public function testInstanceWithoutArguments()
    {
        $bla = new SoapFactory('Webservices');
        static::assertNull($bla->getLogger());
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     */
    public function testInstanceWithoutMandatoryValues()
    {
        SoapFactory::build('Webservices')->create([]);
    }

    /**
     *
     */
    public function testInstanceWithLoggerAndGuzzle()
    {
        $handler = new TestHandler();
        $logger = new Logger('unit-test', [$handler]);
        $platform = WebservicesConfig::PLATFORM_NAME;

        $soapHeader = new \SoapHeader('http://www.somedomain.nl/', 'lala', 'hihi');
        $soapClient = SoapFactory::build($platform, $logger)->create(
            [
                'username' => 'johndoe',
                'password' => 'fakePassword',
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
     *
     */
    public function testInstanceWithoutLogger()
    {
        $soapClient = SoapFactory::build('Webservices')->create(['username' => 'john', 'password' => 'lalala']);
        static::assertAttributeNotInstanceOf('\Monolog\Logger', 'logger', $soapClient);
    }

    /**
     *
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
}
