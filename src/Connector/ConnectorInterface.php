<?php

namespace Webservicesnl\Connector;

use Webservicesnl\Connector\Adapter\AdapterInterface;

/**
 * Interface ConnectorInterface.
 */
interface ConnectorInterface
{
    /**
     * Return this Connectors AdapterInterface
     *
     * @return AdapterInterface
     */
    public function getAdapter();

    /**
     * returns name of platform.
     *
     * @return string
     */
    public function getPlatform();
}
