<?php

namespace WebservicesNl\Test\Connector\ProtocolAdapter;

use Mockery;
use PHPUnit_Framework_TestCase;
use WebservicesNl\Connector\Client\ClientInterface;
use WebservicesNl\Connector\ProtocolAdapter\SoapAdapter;

/**
 * Class SoapAdapterTest.
 */
class SoapAdapterTest extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $protocolName = 'lalala';
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('getProtocolName')->andReturn($protocolName);

        /** @var ClientInterface $client */
        $instance = new SoapAdapter($client);
        static::assertEquals($protocolName, $instance->getProtocol());
        static::assertAttributeInstanceOf(ClientInterface::class, 'client', $instance);
    }

    public function testAdapter()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('__soapCall')
            ->withArgs(['filter', ['alnum', 'Hello_world!']])
            ->andReturn('Hello world');

        $client->shouldReceive('filter')->withArgs(['filter', ['alnum', 'Hello_world!']])->andReturn('Hello world');

        /** @var ClientInterface $client */
        $instance = new SoapAdapter($client);
        $result = $instance->call('filter', ['alnum', 'Hello_world!']);

        static::assertEquals('Hello world', $result);
    }
}
