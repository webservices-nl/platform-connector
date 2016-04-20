<?php

namespace WebservicesNl\Connector;

use WebservicesNl\Connector\Adapter\AdapterInterface;

/**
 * Interface ConnectorInterface.
 *
 * Interface for platform connector through the webservices API.
 */
interface ConnectorInterface
{
    /**
     * Return this connector's AdapterInterface.
     *
     * @return AdapterInterface
     */
    public function getAdapter();

    /**
     * Returns name of platform.
     *
     * @return string
     */
    public function getPlatform();
}
