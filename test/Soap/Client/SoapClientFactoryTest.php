<?php

namespace Webservicesnl\Test\Soap\Client;

use League\FactoryMuffin\Facade as FactoryMuffin;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

use Webservicesnl\Soap\Client\SoapFactory;

/**
 * Class SoapClientFactoryTest
 * @package Webservicesnl\Test\Soap\Client
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
        $bla = new SoapFactory('webservices');
        $this->assertNull($bla->getLogger());
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\Input\InvalidException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     */
    public function testInstanceWithoutMandatoryValues()
    {
        $soapClient = SoapFactory::build('webservices')->create([]);
    }

    /**
     *
     */
    public function testInstanceWithLoggerAndGuzzle()
    {
        $consoleHandler = new TestHandler();
        $logger = new Logger('unit-test');
        $logger->pushHandler($consoleHandler);

        $soapHeader = new \SoapHeader('http://www.somedomain.nl/', 'lala', 'hihi');
        $soapClient = SoapFactory::build('webservices', $logger)->create(
            [
                'username'    => 'johndoe',
                'password'    => 'fakePassword',
                'soapHeaders' => [$soapHeader],
            ]
        );

        $this->assertTrue($soapClient->hasClient());
        $this->assertAttributeInstanceOf('\Monolog\Logger', 'logger', $soapClient);
        $this->assertAttributeContains($soapHeader, '__default_headers', $soapClient);

        $this->assertTrue($consoleHandler->hasInfoThatContains("Creating a SoapClient for platform 'webservices'"));
        $this->assertTrue($consoleHandler->hasDebugThatContains('Created EndpointManager'));
        $this->assertTrue($consoleHandler->hasDebugThatContains('Created SoapClient'));
    }

    /**
     *
     */
    public function testInstanceWithoutLogger()
    {
        $soapClient = SoapFactory::build('webservices')->create(['username' => 'john', 'password' => 'lalala']);
        $this->assertAttributeNotInstanceOf('\Monolog\Logger', 'logger', $soapClient);
    }

    /**
     *
     */
    public function testInstanceWithoutGuzzle()
    {
        $soapClient = SoapFactory::build('webservices')->create(
            [
                'username'      => 'johndoe',
                'password'      => 'fake',
                'useHttpClient' => false,
            ]
        );

        $this->assertFalse($soapClient->hasClient());
    }
}
