<?php

namespace Webservicesnl\Connector\Adapter;

/**
 * Interface AdapterInterface
 *
 * Contract for the connector adapters
 */
interface AdapterInterface
{
    /**
     * Call the client request function
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
}
