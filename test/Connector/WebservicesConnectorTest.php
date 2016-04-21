<?php

namespace WebservicesNl\Test\Connector;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use WebservicesNl\Connector\ConnectorFactory;

/**
 * Class WebservicesConnectorTest
 */
class WebservicesConnectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group FunctionalTest
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function testInstance()
    {
        $settings = [
            'username' => 'soapclienttest_User',
            'password' => '079045d5fd4b395fbb28255893a05e7d',
            'url'      => 'https://dev.webservices.nl/~peter/WebservicesNl/Services/tronco/local/www/api/soap',
        ];

        $logger = new Logger(__CLASS__);
        $testHandler = new TestHandler();
        $streamHandler = new StreamHandler('/tmp/test.log', LogLevel::INFO);

        $logger->setHandlers([$streamHandler, $testHandler]);

        /** @var \WebservicesNl\Platform\Webservices\Connector $connector */
        $factory = ConnectorFactory::build($settings);
        $factory->setLogger($logger);

        $connector = $factory->create('soap', 'webservices');

        $response = $connector->userViewV2();
        self::assertEquals($response->{'nickname'}, 'soapclienttest_User');
    }
}
