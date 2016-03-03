<?php

namespace Webservicesnl\Connector;

use Webservicesnl\Connector\Adapter\AdapterInterface;

/**
 * Interface ConnectorInterface.
 *
 * @package Webservicesnl\Connector
 */
interface ConnectorInterface
{
    /**
     * @return AdapterInterface
     */
    public function getAdapter();

    /**
     * @return string
     */
    public function getType();
}