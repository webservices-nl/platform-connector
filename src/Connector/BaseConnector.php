<?php

namespace Webservicesnl\Connector;

use Webservicesnl\Connector\Adapter\AdapterInterface;

/**
 * Class BaseConnector.
 *
 * @package Webservicesnl\Connector
 */
abstract class BaseConnector implements ConnectorInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * WsConnector constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return string
     */
    public function getType()
    {
       return basename(str_replace('\\', '/', get_called_class()));
    }
}
