<?php

namespace Webservicesnl\Test\Connector;

use League\FactoryMuffin\Facade as FactoryMuffin;
use Webservicesnl\Connector\ConnectorFactory;
use Webservicesnl\Exception\Client\InputException;

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
     * @expectedException \Webservicesnl\Exception\Client\InputException
     * @expectedExceptionMessage Not a valid platform
     *
     */
    public function testInstanceWithBadPlatform()
    {
        ConnectorFactory::create('fake', 'lala');
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\InputException
     * @expectedExceptionMessage Could not load classes for 'Fake' and 'Webservices'
     */
    public function testInstanceWithBadProtocol()
    {
        ConnectorFactory::create('webservices', 'fake');
    }

    /**
     * @throws \Webservicesnl\Exception\Client\InputException
     */
    public function testInstance()
    {
        $protocol = 'soap';
        $platform = 'webservices';

        $client = ConnectorFactory::create('webservices', $protocol, ['username' => 'lala', 'password' => 'hihi']);

        $this->assertInstanceOf('Webservicesnl\Connector\WebservicesConnector', $client);
        $this->assertInstanceOf('Webservicesnl\Connector\Adapter\SoapAdapter', $client->getAdapter());
        $this->assertEquals($protocol, $client->getAdapter()->getProtocol());
        $this->assertEquals($platform, $client->getPlatform());
    }
}
