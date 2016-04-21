<?php

namespace WebservicesNl\Test\Protocol\Soap\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\RequestException;
use League\FactoryMuffin\Facade as FactoryMuffin;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use WebservicesNl\Common\Endpoint\Manager;
use WebservicesNl\Protocol\Soap\Client\SoapClient;
use WebservicesNl\Protocol\Soap\Client\SoapSettings;

/**
 * SoapClientTest
 */
class SoapClientTest extends \PHPUnit_Framework_TestCase
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
        FactoryMuffin::loadFactories(dirname(dirname(dirname(__DIR__))) . '/Factories');
    }

    /**
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientInstance()
    {
        $logger = new Logger(__CLASS__);
        $testHandler = new TestHandler();
        $streamHandler = new StreamHandler('/tmp/test.log', LogLevel::INFO);

        $logger->setHandlers([$streamHandler, $testHandler]);

        /** @var SoapSettings $settings */
        $settings = FactoryMuffin::instance('WebservicesNl\Protocol\Soap\Client\SoapSettings');

        $manager = new Manager();
        $manager->createEndpoint('https://ws1.webservices.nl/soap_doclit');

        // Create a mock and queue two responses.
        $mock = new MockHandler(
            [
                new Response(202, ['Content-Length' => 0]),
                new RequestException('Error Communicating with Server', new Request('GET', 'login')),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler]);

        $instance = new SoapClient($settings, $manager, $curlClient);
        $instance->setLogger($logger);
        $instance->soapCall('login');

        self::assertEquals('soap', $instance->getProtocolName());

    }
}
