<?php

namespace WebservicesNl\Connector\ProtocolAdapter;

use WebservicesNl\Connector\Client\ClientInterface;

/**
 * AdapterInterface.
 *
 * Contract for the connector adapters.
 * In order to switch out protocols, all client have an protocol adapter.
 */
interface AdapterInterface
{
    /**
     * Call the client request function.
     *
     * @param string $functionName
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function call($functionName, $arguments);

    /**
     * Returns this adapter's protocol.
     *
     * @return string
     */
    public function getProtocol();

    /**
     * @return ClientInterface
     */
    public function getClient();
}
