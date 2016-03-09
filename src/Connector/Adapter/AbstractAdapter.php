<?php

namespace Webservicesnl\Connector\Adapter;

abstract class AbstractAdapter
{
    const PROTOCOL_NAME = 'abstract';

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return static::PROTOCOL_NAME;
    }
}
