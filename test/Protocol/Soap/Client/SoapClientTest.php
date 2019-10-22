<?php

namespace WebservicesNl\Test\Protocol\Soap\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use League\FactoryMuffin\Facade as FactoryMuffin;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use WebservicesNl\Common\Endpoint\Manager;
use WebservicesNl\Protocol\Soap\Client\SoapClient;
use WebservicesNl\Protocol\Soap\Client\SoapSettings;
use WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter;

/**
 * SoapClientTest.
 */
class SoapClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var TestHandler
     */
    protected $testHandler;

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

    public function setUp()
    {
        $this->logger = new Logger(__CLASS__);
        $this->testHandler = new TestHandler();
        $streamHandler = new StreamHandler('/tmp/test.log', LogLevel::INFO);

        $this->logger->setHandlers([$streamHandler, $this->testHandler]);

        $this->manager = new Manager();
        $this->manager->createEndpoint('https://ws1.webservices.nl/soap');
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
        static::assertNull($this->manager->getActiveEndpoint()->getLastConnected());

        // Create a mock and queue successful response.
        $mock = new MockHandler(
            [
                new Response(202, ['Content-Length' => 0]),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setLogger($this->logger);
        $instance->__soapCall('login');

        static::assertNotNull($this->manager->getActiveEndpoint()->getLastConnected());
        static::assertEquals('soap', $instance->getProtocolName());
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @expectedExceptionMessage No active server available
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientInstanceBadCall()
    {
        // Create a mock and queue successful response.
        $mock = new MockHandler(
            [
                new ConnectException('Error Communicating with Server', new Request('GET', 'login')),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setLogger($this->logger);
        $instance->__soapCall('login');
    }

    /**
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientInstanceBadCallWithMultipleEndpoints()
    {
        $manager = new Manager();
        $manager->createEndpoint('https://ws1.webservices.nl/soap');
        $manager->createEndpoint('https://ws2.webservices.nl/soap');

        // Create a mock and queue a bad and successful response.
        $mock = new MockHandler(
            [
                new ConnectException('Error Communicating with Server', new Request('GET', 'login')),
                new Response(202, ['Content-Length' => 0]),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler]);

        $instance = new SoapClient(new SoapSettings(), $manager, $curlClient);
        $instance->setLogger($this->logger);
        $instance->call(['functionName' => 'login']);

        static::assertTrue($manager->getEndpoints()->first()->isError());
        static::assertTrue($this->testHandler->hasError('Endpoint is not responding'));
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\ServerException
     * @expectedExceptionMessage Invalid SoapResponse
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientInstanceWithErrorResponse()
    {
        // Create a mock and queue successful response.
        $mock = new MockHandler(
            [
                new Response(400, ['Content-Length' => 2000], "<?xml version='1.0'><broken><xml></broken>"),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler, 'exceptions' => false]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setConverter(new Converter());
        $instance->setLogger($this->logger);
        $instance->__soapCall('login');
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\ServerException
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientInstanceWithErrorResponseAndExceptionsAreConverted()
    {
        // Create a mock and queue successful response.
        $mock = new MockHandler(
            [
                new Response(400, ['Content-Length' => 2000], "<?xml version='1.0'><broken><xml></broken>"),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler, 'exceptions' => true]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setConverter(new Converter());
        $instance->setLogger($this->logger);
        $instance->__soapCall('login');
    }

    /**
     * @expectedException \SoapFault
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientInstanceWithoutConverter()
    {
        // Create a mock and queue successful response.
        $mock = new MockHandler(
            [
                new Response(400, ['Content-Length' => 2000], "<?xml version='1.0'><broken><xml></broken>"),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler, 'exceptions' => false]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setLogger($this->logger);
        $instance->__soapCall('login');
    }

    /**
     * @expectedException \SoapFault
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientInstanceWithBadRequest()
    {
        $mock = new MockHandler(
            [
                new Response(202, ['Content-Length' => 0]),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler, 'exceptions' => false]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setLogger($this->logger);
        $instance->__soapCall('/login');
    }

    /**
     * @throws \SoapFault
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testSoapClientGetSignatures()
    {
        $mock = new MockHandler();
        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler, 'exceptions' => false]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $signatures = $instance->getSignatures();

        static::assertArrayHasKey('login', $signatures);
        static::assertSame(['username', 'password'], $signatures['login']);
    }

    /**
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientCallWithWrongOrderArguments()
    {
        $expectedRequestBody = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.webservices.nl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
  <SOAP-ENV:Header/>
  <SOAP-ENV:Body>
    <ns1:login>
      <username xsi:type="xsd:string">a</username>
      <password xsi:type="xsd:string">b</password>
    </ns1:login>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>

EOT;

        $mock = new MockHandler(
            [
                function(Request $request) use ($expectedRequestBody) {
                    $dom = new \DOMDocument();
                    $dom->preserveWhiteSpace = false;
                    $dom->formatOutput = true;
                    $dom->loadXML($request->getBody()->getContents());

                    static::assertSame($expectedRequestBody, $dom->saveXML());
                    return new Response(202, ['Content-Length' => 0]);
                },
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler, 'exceptions' => false]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setLogger($this->logger);
        $instance->__soapCall('login', ['password' => 'b', 'username' => 'a']);
    }

    /**
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \SoapFault
     */
    public function testSoapClientCallUnknownArguments()
    {
        $mock = new MockHandler(
            [
                new Response(202, ['Content-Length' => 0]),
            ]
        );

        $handler = HandlerStack::create($mock);
        $curlClient = new Client(['handler' => $handler, 'exceptions' => false]);

        $instance = new SoapClient(new SoapSettings(), $this->manager, $curlClient);
        $instance->setLogger($this->logger);
        $instance->__soapCall('login', ['unknown' => 'argument', 'username' => 'a', 'password' => 'b']);
        $this->testHandler->hasWarningThatMatches('~Invalid argument [unknown]~');
    }
}
