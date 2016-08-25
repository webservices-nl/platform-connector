<?php

namespace WebservicesNl\Connector\ProtocolAdapter;

use WebservicesNl\Connector\Client\ClientInterface;

/**
 * AbstractAdapter.
 *
 * AbstractAdapter, base class for extending all Connector interface Adapters (adapter pattern).
 *
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Constructor.
     *
     * take care the sending the message over the client and protocol related crap
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return $this->client->getProtocolName();
    }
}
