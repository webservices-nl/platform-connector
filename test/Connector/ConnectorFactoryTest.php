<?php

namespace WebservicesNl\Test\Connector;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Connector\ConnectorFactory;
use WebservicesNl\Connector\WebservicesConnector;

class ConnectorFactoryTest extends \PHPUnit_Framework_TestCase
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
        FactoryMuffin::loadFactories(dirname(__DIR__) . '/Factories');
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not load classes for platform: 'Fake' and protocol: ''
     * @throws InputException
     */
    public function testInstanceWithBadPlatform()
    {
        ConnectorFactory::build()->create('Fake', null);
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not load classes for platform: 'Webservices' and protocol: 'FakeProtocol'
     * @throws InputException
     */
    public function testInstanceWithBadProtocol()
    {
        ConnectorFactory::build()->create('Webservices', 'FakeProtocol');
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws InputException
     */
    public function testInstance()
    {
        $protocol = 'soap';

        $client = ConnectorFactory::build(['username' => 'lala', 'password' => 'hihi'])
            ->create(WebservicesConnector::PLATFORM_NAME, 'soap');

        static::assertInstanceOf('WebservicesNl\Connector\WebservicesConnector', $client);
        static::assertInstanceOf('WebservicesNl\Connector\Adapter\SoapAdapter', $client->getAdapter());
        static::assertEquals($protocol, $client->getAdapter()->getProtocol());
        static::assertEquals(WebservicesConnector::PLATFORM_NAME, $client->getPlatform());
    }
}
