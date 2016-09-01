<?php

namespace WebservicesNl\Test\Connector;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Connector\ConnectorFactory;
use WebservicesNl\Platform\Webservices\Connector;

/**
 * Class ConnectorFactoryTest
 *
 * @package WebservicesNl\Test\Connector
 */
class ConnectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not find a platformConfig for Fake
     * @throws InputException
     */
    public function testInstanceWithBadPlatform()
    {
        ConnectorFactory::build(['username' => 'bla', 'password' => 'secret'])->create(null, 'Fake');
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Could not find a factory for Nope
     * @throws InputException
     */
    public function testInstanceWithBadProtocol()
    {
        ConnectorFactory::build(['username' => 'bla', 'password' => 'secret'])->create('Nope', 'Webservices');
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     * @throws InputException
     */
    public function testInstanceWithMissingArguments()
    {
        ConnectorFactory::build(['password' => 'secret'])->create('Nope', 'Webservices');
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws InputException
     */
    public function testInstance()
    {
        $protocol = 'soap';
        $platform = 'webservices';
        $connector = ConnectorFactory::build(['username' => 'lala', 'password' => 'hoho'])
            ->create($protocol, $platform);

        static::assertInstanceOf('WebservicesNl\Platform\Webservices\Connector', $connector);
        static::assertInstanceOf('WebservicesNl\Connector\ProtocolAdapter\SoapAdapter', $connector->getAdapter());
        static::assertEquals($protocol, $connector->getAdapter()->getProtocol());
        static::assertEquals(Connector::PLATFORM_NAME, $connector->getPlatform());
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws InputException
     */
    public function testConnectorFactoryInstanceBySettingLogger()
    {
        $testHandler = new TestHandler();
        $logger = new Logger(__CLASS__);
        $logger->pushHandler($testHandler);

        $factory = ConnectorFactory::build(['username' => 'something', 'password' => 'secret']);
        $factory->setLogger($logger);
        $factory->create('soap', 'webservices');

        static::assertInstanceOf('Psr\Log\LoggerInterface', $factory->getLogger());
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws InputException
     */
    public function testConnectorFactoryInstanceByConstruct()
    {
        $testHandler = new TestHandler();
        $logger = new Logger(__CLASS__);
        $logger->pushHandler($testHandler);

        $factory = ConnectorFactory::build(['username' => 'something', 'password' => 'secret'], $logger);
        $factory->create('soap', 'webservices');

        static::assertInstanceOf('Psr\Log\LoggerInterface', $factory->getLogger());
    }
}
