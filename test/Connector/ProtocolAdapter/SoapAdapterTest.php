<?php

namespace WebservicesNl\Test\Connector\ProtocolAdapter;

use WebservicesNl\Connector\Client\ClientInterface;
use WebservicesNl\Connector\ProtocolAdapter\SoapAdapter;

/**
 * Class SoapAdapterTest.
 */
class SoapAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testInstance()
    {
        $protocolName = 'lalala';
        $client = \Mockery::mock('WebservicesNl\Connector\Client\ClientInterface');
        $client->shouldReceive('getProtocolName')->andReturn($protocolName);

        /** @var ClientInterface $client */
        $instance = new SoapAdapter($client);
        static::assertEquals($protocolName, $instance->getProtocol());
        static::assertAttributeInstanceOf('WebservicesNl\Connector\Client\ClientInterface', 'client', $instance);
    }
}
