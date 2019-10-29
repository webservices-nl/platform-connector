<?php

namespace WebservicesNl\Test\Connector;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Connector\ConnectorFactory;
use WebservicesNl\Connector\ProtocolAdapter\SoapAdapter;
use WebservicesNl\Platform\Webservices\Connector;

/**
 * Class ConnectorFactoryTest
 */
class ConnectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws InputException
     */
    public function testInstanceWithBadPlatform()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Could not find a platformConfig for Fake');
        ConnectorFactory::build(['username' => 'bla', 'password' => 'secret'])->create(null, 'Fake');
    }

    /**
     * @throws InputException
     */
    public function testInstanceWithBadProtocol()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Could not find a factory for Nope');
        ConnectorFactory::build(['username' => 'bla', 'password' => 'secret'])->create('Nope', 'Webservices');
    }

    /**
     * @expectedExceptionMessage
     */
    public function testInstanceWithMissingArguments()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Not all mandatory config credentials are set');

        ConnectorFactory::build(['password' => 'secret'])->create('Nope', 'Webservices');
    }

    /**
     * @throws InputException
     */
    public function testInstance()
    {
        $protocol = 'soap';
        $platform = 'webservices';
        $connector = ConnectorFactory::build(['username' => 'lala', 'password' => 'hoho'])
            ->create($protocol, $platform);

        static::assertInstanceOf(Connector::class, $connector);
        static::assertInstanceOf(SoapAdapter::class, $connector->getAdapter());
        static::assertEquals($protocol, $connector->getAdapter()->getProtocol());
        static::assertEquals(Connector::PLATFORM_NAME, $connector->getPlatform());
    }

    /**
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

        static::assertInstanceOf(LoggerInterface::class, $factory->getLogger());
    }

    /**
     * @throws InputException
     */
    public function testConnectorFactoryInstanceByConstruct()
    {
        $testHandler = new TestHandler();
        $logger = new Logger(__CLASS__);
        $logger->pushHandler($testHandler);

        $factory = ConnectorFactory::build(['username' => 'something', 'password' => 'secret'], $logger);
        $factory->create('soap', 'webservices');

        static::assertInstanceOf(LoggerInterface::class, $factory->getLogger());
    }
}
