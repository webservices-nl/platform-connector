<?php

namespace Webservicesnl\Connector;

use Webservicesnl\Connector\Adapter\AdapterInterface;

/**
 * Class BaseConnector.
 *
 * @package Webservicesnl\Connector
 */
abstract class AbstractConnector implements ConnectorInterface
{
    const PLATFORM_NAME = 'abstract';

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
     * @inheritdoc
     */

    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @inheritdoc
     */
    public function getPlatform()
    {
       return static::PLATFORM_NAME;
    }
}
