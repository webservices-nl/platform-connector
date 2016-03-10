<?php

namespace Webservicesnl\Test\Connector;

use League\FactoryMuffin\Facade as FactoryMuffin;
use Webservicesnl\Connector\ConnectorFactory;

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
     * @expectedException \Webservicesnl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not load classes for 'Fake' and 'Lala'
     *
     */
    public function testInstanceWithBadPlatform()
    {
        ConnectorFactory::build()->create('fake', 'lala');
    }

    /**
     * @expectedException \Webservicesnl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not load classes for 'Webservices' and 'FakeProtocol'
     */
    public function testInstanceWithBadProtocol()
    {
        ConnectorFactory::build()->create('Webservices', 'FakeProtocol');
    }

    /**
     * @throws \Webservicesnl\Common\Exception\Client\InputException
     */
    public function testInstance()
    {
        $protocol = 'soap';
        $platform = 'webservices';

        $client = ConnectorFactory::build(['username' => 'lala', 'password' => 'hihi'])->create('webservices', $protocol);

        static::assertInstanceOf('Webservicesnl\Connector\WebservicesConnector', $client);
        static::assertInstanceOf('Webservicesnl\Connector\Adapter\SoapAdapter', $client->getAdapter());
        static::assertEquals($protocol, $client->getAdapter()->getProtocol());
        static::assertEquals($platform, $client->getPlatform());
    }
}
