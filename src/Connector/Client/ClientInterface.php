<?php

namespace WebservicesNl\Connector\Client;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * Returns the protocol name.
     *
     * @return string
     */
    public function getProtocolName();

    /**
     * Make a request.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function call(array $args = []);
}
